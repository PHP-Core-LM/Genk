<?php
/**
 * Created by PhpStorm.
 * Author: Lê Minh Hổ
 * Date: 4/12/2019
 * Time: 2:16 PM
 */

include_once "simple_html_dom.php";
include_once "Crawl.php";
include_once "model/bean/Post.php";

class Leech
{
    private $url = null; //Đường dẫn con của trang đang leech

    //Thông tin trang leech
    private $baseURL = "http://genk.vn"; //Đường dẫn gốc của trang đang leech
    private $URIs = array(
        "Mobile" => "/mobile.chn",
        "Tablet" => "/may-tinh-bang.chn",
        "Digital_Marketing" => "/digital-marketing.chn",
        "Media" => "/media.chn"
    ); //Danh sách các trang con có thể leech

    /**
     * @todo Đọc nội dung toàn bộ bài viết trong baseURL theo từng mục URI với số lượng pageLeech
     * @param $baseURL
     * @param $URIs
     * @param $pageLeech
     * @return array
     */
    public function leechAllData($pageLeech)
    {
        $result = array();
        foreach ($this->URIs as $URI)
        {
            $result = array_merge($result, $this->leechPost($URI, $pageLeech));
        }
        return $result;
    }


    /**
     * @todo Leech tiêu đề  website có $url
     * @param $uri
     * @param $pageNum
     * @return array
     */
    public function leechPost($uri, $pageNum)
    {
        $result = array(); //Mảng kết quả trả về
        $crawler = new Crawl(); //Khởi tạo đối tượng Crawl

        $html = $crawler->getSourceHTML($this->baseURL.$uri); //Lấy source code của url
        $info = $this->getAjaxInfo($html);//Lấy thông số trong source để Ajax dùng
        for ($page = 1; $page<=$pageNum; $page++){
            $requetURL = $this->initAjaxURL($this->baseURL.$info["uri"],
                $page, $info["ZoneId"], $info["ExcluId"]); //Khởi tạo đường dẫn để request theo từng page
            $respone = $crawler->getSourceHTML($requetURL); //Mã nguồn page nhận được khi request
            $result = array_merge($result, $this->readPost($respone));
        }
        return $result;
    }


    /**
     * @todo Leech nội dung bài viết
     * @param $url
     * @return array
     */
    private function leechContentPost($url){
        $crawler = new Crawl(); //Khởi tạo đối tượng Crawl

        $html = $crawler->getSourceHTML($url);
        return $this->readContentPost($html);
    }


    /**
     * @todo Trích lọc thông tin trong tiêu đề bài viết
     * @param $html
     * @return array
     * Cấu trúc mảng trả về:
     * - title tiêu đề bài đăng
     * - thumbnail ảnh tiêu đề bài đăng
     * - author -> tác giả bài viết
     * - time -> thời gian đăng bài viết
     * - descript -> mô tả vắn tắt bài viết
     * - content -> nội dung chính bài viết
     * - content-img -> danh sách link ảnh trong bài viết
     * Lưu ý:
     * - Mã hóa ảnh trong nội dung bài viết {content} thành thẻ {id} với id là index của ảnh trong mảng content-img
     * - Mã hóa <br> trong nội dung bài viết thành thẻ {n}
     */
    private function readPost($html)
    {
        $posts = array(); //Mảng toàn bộ nội dung bài đăng

        $titles = $html->find("li.knswli a"); //Danh sách thông tin tiêu đề bài đăng
        foreach ($titles as $title) {
            //Loại bỏ đường link không có thumbnail
            if (count($title->children()) > 0) {
                $temp = array(); //Mảng chứa nội dung tiêu đề đọc được
                $temp["ID"] = substr($title->href, strripos($title->href, "-") + 1,
                    strlen($title->href) - strripos($title->href, "-") - 5);
                $temp['Title'] = $title->title;
                $temp['Thumbnail'] = $title->getElementByTagName("img")->src;
                $posts[] = array_merge($temp, $this->leechContentPost($this->baseURL.$title->href));
            }
        }

        return $posts;
    }


    /**
     * @todo Trích lọc nội dung bài viết
     * @param $html
     * @return array
     * Cấu trúc mảng result:
     * - author -> tác giả bài viết
     * - time -> thời gian đăng bài viết
     * - descript -> mô tả vắn tắt bài viết
     * - content -> nội dung chính bài viết
     * - content-img -> danh sách link ảnh trong bài viết
     */
    private function readContentPost($html){
        $result = array();

        //Lấy thông tin tác giả và ngày đăng
        $info = $html->find("div.kbwc-meta");
        if ($info != null) {
            $result['Author'] = ($info[0]->find("span.kbwcm-author")[0])->plaintext;
            $result['Date'] = ($info[0]->find("span.kbwcm-time")[0])->title;
            $result['Date'] = substr($result['Date'], 0, strpos($result['Date'], 'T'));
        }

        //Lấy thông tin mô tả bài đăng
        $content = $html->find("div.klw-new-content");
        if ($content != null){
            $result['Descript'] = ($content[0]->find("h2.knc-sapo")[0])->plaintext;

            /*
             * Vì mỗi đoạn nội dung được các nhau bởi thẻ <p>
             * - tiến hành tách, ghép các thẻ <p> thành nội dung
             * - mã hóa ảnh thành thẻ {img-xx} với xx là số thứ tự của ảnh
             * - mã hóa <br> thành thẻ {n}
             */
            $contents = ($html->getElementById("ContentDetail"))->children();
            $slg = 0; //Số lượng ảnh trong nội dung, được dùng làm số thứ tự cho ảnh
            foreach ($contents as $content) {
                if ($content->tag == "p") { //Chỉ chứa text thông thường
                    if (array_key_exists("Content", $result) == true) {
                        $result['Content'] = $result['Content'] . $content->innertext . "{n}";
                    }
                    else {
                        $result['Content'] = $content->innertext . "{n}";
                    }
                }
                else //Chứa nội dung khác ngoài text
                {
                    if ($content->type == "Photo") { //Chứa ảnh

                        $idImg = "img-".strval($slg); //Mã lưu trữ của ảnh
                        $imgs = $content->find("img"); //Tìm danh sách ảnh trong tag
                        if ($imgs != null) {
                            $img = $imgs[0]->src; //Link ảnh

                            //Chèn ảnh vào phần nội dung
                            if (array_key_exists("Content", $result) == true) {
                                $result['Content'] = $result['Content'] . "{" . $idImg . "}{n}";
                            }
                            else {
                                $result['Content'] = "{" . $idImg . "}{n}";
                            }

                            //Cập nhật link ảnh vừa chèn
                            if (array_key_exists("Content-Img", $result) == false) {
                                $result['Content-Img'] = array();
                            }
                            $result['Content-Img'][$idImg] = $img;
                            $slg += 1; //Tăng số lượng ảnh đã có
                        }
                    }
                }
            }
        }

        $result['Content-Img'] = json_encode($result['Content-Img']); //Encode mảng ảnh thành chuỗi json để lưu trữ
        return $result;
    }


    /**
     * @todo Truy vấn thông tin cần thiết cho gói tin request bài viết
     * @param $html
     * @return array
     */
    private function getAjaxInfo($html)
    {
        $result = array();
        $result["uri"] = $html->getElementById("hdPageUrlAjax")->value;
        $result["ZoneId"] = $html->getElementById("hdZoneId")->value;
        $result["ExcluId"] = $html->getElementById("hdExcluId")->value;
        return $result;
    }


    /**
     * @todo Khởi tạo url cho ajax request
     * @param $url
     * @param $page
     * @param $zoneID
     * @param $excluID
     * @return mixed
     */
    private function initAjaxURL($url, $page, $zoneID, $excluID)
    {
        $url = str_replace("{0}", $page, $url);
        $url = str_replace("{1}", $zoneID, $url);
        $url = str_replace("{2}", $excluID, $url);

        return $url;
    }


    /**
     * @todo Lấy ra uri cần leech
     * @param $index
     * @return string
     */
    public function getURIs($index){
        switch ($index){
            case MOBILE:{
                return $this->URIs['Mobile'];
            }
            case TABLET:{
                return $this->URIs['Tablet'];
            }
            case DIGITAL_MARKETING:{
                return $this->URIs['Digital_Marketing'];
            }
            case MEDIA:{
                return $this->URIs['Media'];
            }
            default:{
                return "";
            }
        }
    }
}