<?php
  if (!defined('BASEPATH')) exit('No direct script access allowed');

  class Feedburner
  {
    const API = 'https://feedburner.google.com/api/awareness/1.0/GetFeedData?uri=';

    private $date;
    private $circulation;    // An approximate measure of the number of individuals for whom your feed has been requested in the 24 hour period described by date
    private $reach;          // Reach is the total number of people who have taken action — viewed or clicked — on the content in your feed.
    private $hits;           // The exact number of requests for your FeedBurner feed during the 24 hour period described by date

    private $expire = 7200;  // expire cache every two hours (time in seconds)

    public function __construct()
    {
      $this->CI =& get_instance();
      $this->CI->load->model('feeds');
    }

    public function get_feed($feed_name)
    {
      $cached = $this->CI->feeds->get_cached($feed_name);
      if (is_object($cached))
      {
        if (time() <= strtotime($cached->created_at)+$this->expire)
        {
          // cache not expired yet, return cached contents
          $this->cache_to_vars($cached);
        }
        else
        {
          // cache expired, re-leech & re-generate cache
          if ($this->request_to_vars($feed_name))
          {
            $this->CI->feeds->update_feed($feed_name,array('date'=>$this->date,'circulation'=>$this->circulation,'reach'=>$this->reach,'hits'=>$this->hits,'created_at'=>'now()'));
          }
          else
          {
            // fallback: error while new getting request, keep older cached values
            $this->cache_to_vars($cached);
          }
        }
      }
      else
      {
        // no cache yet, create first
        if ($this->request_to_vars($feed_name))
        {
          $this->CI->feeds->insert_feed(array('uri'=>$feed_name,'date'=>$this->date,'circulation'=>$this->circulation,'reach'=>$this->reach,'hits'=>$this->hits,'created_at'=>'now()'));
        }
        else
        {
          // panic. error while first request & no cache to fallback to
          return FALSE;
          exit;
        }
      }
      return array('date'=>(string)$this->date, 'circulation'=>(string)$this->circulation, 'reach'=>(string)$this->reach, 'hits'=>(string)$this->hits);
    }

    private function request_to_vars($feed_name)
    {
      $res = $this->request($feed_name);
      if ($res && is_object($res->feed) && is_object($res->feed->entry) && $res->feed->entry['circulation']>0)
      {
        // only update vars if non-empty data received
        $this->date        = $res->feed->entry['date'];
        $this->hits        = $res->feed->entry['hits'];
        $this->reach       = $res->feed->entry['reach'];
        $this->circulation = $res->feed->entry['circulation'];
        return TRUE;
      }
      else
      {
        return FALSE;
      }
    }

    private function cache_to_vars($cached)
    {
      if (is_object($cached))
      {
        $this->date        = $cached->date;
        $this->hits        = $cached->hits;
        $this->reach       = $cached->reach;
        $this->circulation = $cached->circulation;
        return TRUE;
      }
      else
      {
        return FALSE;
      }
    }

    private function request($feed_name)
    {
      try
      {
        $ch  = curl_init(self::API.$feed_name);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        //
        // MAMP does not install the proper CA cert bundle (http://curl.haxx.se/ca/cacert.pem) for
        // verifying the peer's certificate, so accessing the https Feedburner API gives an SSL error
        // to avoid this, explicitly prevent cURL from trying:
        //
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $res = curl_exec($ch);
        curl_close($ch);
        $xml = new SimpleXMLElement($res);

        return $xml;
      }
      catch (Exception $e)
      {
        return FALSE;
      }
    }
  }

/* End of file Feedburner.php */
