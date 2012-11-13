<?php
namespace FS\SolrBundle\Query;

class SolrResponse {
    public function __construct($solrResponse) {
        $this->solrResponse = $solrResponse;
    }

    public function getResult() {
        if (!array_key_exists('response', $this->solrResponse)) {
            return array(); 
        }   
            
        if ($this->solrResponse['response']['docs'] == false) {
            return array(); 
        }
        
        return  $this->solrFacade->toEntity(
                    $this->solrResponse['response']['docs'],
                    $this->getResultEntity()
                );
    }

    public function getSuggestions() {
        if (!array_key_exists('spellcheck', $this->solrResponse)) {
            return array(); 
        }   
        if ($this->solrResponse['spellcheck']['suggestions'] == false) {
            return array(); 
        }

        $suggestions = array();
        $corrections = $this->solrResponse['spellcheck']['suggestions'];

        foreach ($corrections as $misspelled => $correction) {
            $suggestions[$misspelled] = $correction['suggestion'];
        }

        return $suggestions;
    }

    public function setFacade($solrFacade) {
        $this->solrFacade = $solrFacade;
    }

    /**
     * @return the $entity
     */
    public function getResultEntity() {
        return $this->entity;
    }
    
    /**
     * @param object $entity
     */
    public function setResultEntity($entity) {
        $this->entity = $entity;
    }   
}