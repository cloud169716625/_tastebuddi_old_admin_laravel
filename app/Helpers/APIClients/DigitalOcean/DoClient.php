<?php


namespace App\Helpers\APIClients\DigitalOcean;


class DoClient
{
    private $client;
    private $api_key;
    private $base_uri = 'https://api.digitalocean.com';
    private $server_domain;
    private $server_ip;

    private $records;
    private $error;

    public function __construct( $api_key, $domain , $ip_address )
    {
        $this->client = new \GuzzleHttp\Client(['base_uri' => $this->base_uri ]);
        $this->api_key = $api_key;
        $this->server_domain = $domain;
        $this->server_ip = $ip_address;
    }

    /**
     * @return mixed
     * Get all records given a domain
     */
    public function getRecords()
    {
        $response = $this->client->get( 'v2/domains/'.$this->server_domain.'/records', [
            'headers' => [ 'Authorization' => 'Bearer '.$this->api_key ]
        ])->getBody();

        $response_object    = json_decode( $response );
        $this->records      = $response_object->domain_records;

        return $this->records;
    }

    /**
     * Add a A record name to a given domain
     * @param $record_name
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addARecord( $record_name  )
    {
        if( $this->checkHasARecord( $record_name ) ) {
            $this->error = 'A record '.$record_name.' exist already for domain '.$this->server_domain ;
            return false;
        }else{
            $response = $this->client->request('POST', 'v2/domains/'.$this->server_domain.'/records' , [
                'headers' => [ 'Authorization' => 'Bearer '.$this->api_key ],
                'form_params'   => [
                    "type"      => "A",
                    "name"      => $record_name,
                    "data"      => $this->server_ip,
                    "priority"  => null,
                    "port"      => null,
                    "ttl"       => 3600,
                    "weight"    => null,
                    "flags"     => null,
                    "tag"       => null
                ]
            ]);

            return $response;
        }

    }

    /**
     * Get Error
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Cehck if A recrod was already set
     * @param $record_name
     * @return bool
     */
    private function checkHasARecord( $record_name ){
        if( ! $this->records ){
            $this->getRecords();
        }

        return $this->search( $record_name );
    }

    /**
     * Search if recorn name exists
     * 
     * @param $record_name
     * @return bool
     */
    private function search( $record_name ){
        foreach ( $this->records as $a ){
            if( $a->type == 'A' && $a->name == $record_name ){
                return $a;
            }
        }

        return false;
    }
}