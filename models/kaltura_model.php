<?php

class kaltura_model extends CI_Model {

    protected $client;
    private $config;

    public function __construct() {
        $this->config = array(
            'PARTNER_ID' => $this->config->item('kaltura_api_partner_id'),
            'SECRET' => $this->config->item('kaltura_api_secret'),
            'ADMIN_SECRET' =>  $this->config->item('kaltura_api_admin_secret'),
            'SERVICE_URL' => $this->config->item('kaltura_api_server'),
            'UPLOAD_FILE'
        );
        require_once(APPPATH . 'libraries/kaltura/KalturaClient.php');
        // $this->load->library('kaltura/KalturaClient');
        $kConfig = new KalturaConfiguration($this->config['PARTNER_ID']);
        $kConfig->serviceUrl = $this->config['SERVICE_URL'];
        $this->client = new KalturaClient($kConfig);
    }

    private function getKalturaClient($isAdmin = TRUE) {

        $userId = "0";
        $sessionType = ($isAdmin) ? KalturaSessionType::ADMIN : KalturaSessionType::USER;
        try {
            $ks = $this->client->generateSession($this->config['ADMIN_SECRET'], $userId, $sessionType, $this->config['PARTNER_ID']);
            $this->client->setKs($ks);
        } catch (Exception $ex) {
            die("could not start session - check configurations class");
        }


        return $this->client;
    }

    public function getKs() {
        $this->getKalturaClient();
        return $this->client->getKs();
    }

    public function getPlaylist() {

        try {
            $client = $this->getKalturaClient();
            $results = $client->playlist->listAction();
            $entry = (array) $results->objects;
            return $entry;
        } catch (Exception $ex) {
            die($ex->getMessage());
        }
    }

    public function getNewsMedia() {

        try {
            $client = $this->getKalturaClient();
            $filter = new KalturaMediaEntryFilter();
            $filter->orderBy = '-createdAt';
            $pager = new KalturaFilterPager();
            $results = $client->media->listAction($filter, $pager);
            $entry = $results->objects;
            return $entry;
        } catch (Exception $ex) {
            die($ex->getMessage());
        }
    }

    public function getMostVideos() {
        try {
            $client = $this->getKalturaClient();
            $filter = new KalturaMediaEntryFilter();
            $filter->orderBy = '-views';
            $pager = new KalturaFilterPager();
            $results = $client->media->listAction($filter, $pager);
            $entry = $results->objects;
            return $entry;
        } catch (Exception $ex) {
            die($ex->getMessage());
        }
    }

    public function getCategoryMostViews($id) {
        try {
            $client = $this->getKalturaClient();
            $filter = new KalturaMediaEntryFilter();
            $filter->categoriesIdsMatchAnd = $id;
            $filter->orderBy = '-views';
            $pager = new KalturaFilterPager();
            $results = $client->media->listAction($filter, $pager);
            $entry = $results->objects;
            return $entry;
        } catch (Exception $ex) {
            die($ex->getMessage());
        }
    }

    public function getCategoriesWhereId($id) {

        try {
            $client = $this->getKalturaClient();
            $results = $client->category->get($id);
            // $results = $client->category->listAction();
            $entry = $results;
            return $entry;
        } catch (Exception $ex) {
            die($ex->getMessage());
        }
    }

    public function getCategories() {

        try {
            $client = $this->getKalturaClient();
            $results = $client->category->listAction();
            $entry = $results->objects;
            return $entry;
        } catch (Exception $ex) {
            die($ex->getMessage());
        }
    }

    public function getData() {

        try {
            $client = $this->getKalturaClient();
            $results = $client->data->listAction();
            $entry = $results->objects;
            return $entry;
        } catch (Exception $ex) {
            die($ex->getMessage());
        }
    }

    public function getSearch($keyWord) {

        try {
            $client = $this->getKalturaClient();
            $filter = new KalturaMediaEntryFilter();
            $results = $client->media->listAction($filter);
            $table = array();
            $table[] = array("id", "name", "description");
            foreach ($results->objects as $entry) {
                $row = array();
                $row[] = $entry->id;
                $row[] = $entry->name;
                $row[] = $entry->description;
                $table[] = $row;
                if (preg_match("/" . $keyWord . "/i", $entry->name) || preg_match("/" . $keyWord . "/i", $entry->description)) {
                    $result[] =  self::getMedia($entry->id);
                }
            }


            return $result;
        } catch (Exception $ex) {
            die($ex->getMessage());
        }
    }

    /*
      public function getSearch($keyWord) {

      try {
      $client = $this->getKalturaClient();
      $filter = new KalturaMediaEntryFilter();
      $filter->searchTextMatchAnd = $keyWord;
      $pager = null;
      $results = $client->media->listAction($filter, $pager);
      return $results->objects;
      } catch (Exception $ex) {
      die($ex->getMessage());
      }
      }
     */

    public function getMedia($id) {

        try {
            $client = $this->getKalturaClient();
            $entry = $client->media->get($id);
            return $entry;
        } catch (Exception $ex) {
            die($ex->getMessage());
        }
    }

    public function getCategoryVideos($id) {
        try {
            $client = $this->getKalturaClient();
            $filter = new KalturaMediaEntryFilter();
            $filter->categoriesIdsMatchAnd = $id;
            $pager = new KalturaFilterPager();
            $results = $client->media->listAction($filter, $pager);
            $entry = $results->objects;
            return $entry;
        } catch (Exception $ex) {
            die($ex->getMessage());
        }
    }

}