<?php
namespace FS\SolrBundle\Query;

use FS\SolrBundle\SolrFacade;

class SolrQuery extends AbstractQuery {
	
	/**
	 * @var array
	 */
	private $mappedFields = array();
	
	/**
	 * @var array
	 */
	private $searchTerms = array();
	
	/**
	 * @var bool
	 */
	private $useAndOperator = false;
	
	/**
	 * 
	 * @var SolrFacade
	 */
	private $solrFacade = null;
	
	/**
	 * @param SolrFacade $solr
	 */
	public function __construct(SolrFacade $solr) {
		parent::__construct();
		
		$this->solrFacade = $solr;
	}
	
	/**
	 * @return array
	 */
	public function execute() {
		return $this->solrFacade->query($this);		
	}
	
	/**
	 * @return array
	 */
	public function getMappedFields() {
		return $this->mappedFields;
	}

	/**
	 * @param array $mappedFields
	 */
	public function setMappedFields($mappedFields) {
		$this->mappedFields = $mappedFields;
	}
	
	/**
	 * @param bool $strict
	 */
	public function useAndOperator($strict) {
		$this->useAndOperator = $strict;
	}

	public function enableSpellChecker() {
		$this->solrQuery->addParam('spellcheck', 'true');
		$this->solrQuery->addParam('spellcheck.build', 'true');
		$this->solrQuery->addParam('qt', '/spell');
		
		return $this;
	}
	
	/**
	 * @return array
	 */
	public function getSearchTerms() {
		return $this->searchTerms;
	}

	/**
	 * @param array $value
	 */
	public function queryAllFields($value) {
		$this->setUseAndOperator(false);
		
		foreach ($this->mappedFields as $documentField => $entityField) {
			$this->searchTerms[$documentField] = $value;			
		}
	}
	
	/**
	 * 
	 * @param string $field
	 * @param string $value
	 * @return SolrQuery
	 */
	public function addSearchTerm($field, $value) {
		$documentFieldsAsValues = array_flip($this->mappedFields);
		
		if (array_key_exists($field, $documentFieldsAsValues)) {
			$documentFieldName = $documentFieldsAsValues[$field];
			
			$this->searchTerms[$documentFieldName] = $value;
		}
		
		return $this;
	}
	
	/**
	 * @param string $field
	 * @return SolrQuery
	 */
	public function addField($field) {
		$entityFieldNames = array_flip($this->mappedFields);
		if (array_key_exists($field, $entityFieldNames)) {
			$this->solrQuery->addField($entityFieldNames[$field]);
		}
		
		return $this;
	}

	/**
	 * @return string
	 */
	public function getQueryString() {
		$term = '';
		if (count($this->searchTerms) == 0) {
			return $term;
		}
		
		$logicOperator = 'AND';
		if (!$this->useAndOperator) {
			$logicOperator = 'OR';
		}		
		
		$termCount = 1;
		foreach ($this->searchTerms as $fieldName => $fieldValue) {
			$term .= $fieldName .':*'.$fieldValue.'*';
			if ($termCount < count($this->searchTerms)) {
				$term .= ' '. $logicOperator .' ';
			}
			
			$termCount++;
		}
		
		return $term;
	}
}

?>