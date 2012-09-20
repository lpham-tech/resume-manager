<?php

class ResumeController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $view = new Zend_View();
        $view->headScript()->appendFile ( '/js/jquery-1.7.1.js' );
        $view->headLink()->appendStylesheet ( '/js/themes/base/jquery.ui.all.css' );
        $view->headScript()->appendFile ( '/js/jquery.ui.datepicker.js' );
        $view->headScript()->appendFile ( '/js/jquery.ui.core.js' );
    }

    public function indexAction()
    {
		$this->view->number = 123456;
		$this->view->name = 'phan duy canh';
		
		$resume = new Default_Model_ResumeMapper();
		$rows = $resume->fetchAll();
//print_r($rows);
        $this->view->rows = $rows;
    }
    
	
	public function personalInfoAction()
    {
		//$this->_redirect('resume/experience');
    }
	
	public function saveResumeAction()
    {
    	$post = $this->getRequest()->getPost();
    	$date = new DateTime($post['birthday']);
        $birthday = $date->format('Y-m-d');
        
		$resumeRowset = new Default_Model_Resume();
			
		$resumeRowset->setResumeCode('R-01');
		$resumeRowset->setFullName($post['full_name']);
		$resumeRowset->setBirthday($birthday);
		$resumeRowset->setGender($post['gender']);
		$resumeRowset->setMaritaStatus($post['marital_status']);
		$resumeRowset->setStatus('Active');
		$resumeRowset->setEmail1($post['email1']);
		$resumeRowset->setEmail2($post['email2']);
		$resumeRowset->setMobile1($post['mobile1']);
		$resumeRowset->setMobile2($post['mobile2']);
		$resumeRowset->setTel($post['tel']);
		$resumeRowset->setAddress($post['address']);
		$resumeRowset->setProvinceId(1);
		$resumeRowset->setCreatedDate(date('Y-m-d'));
		$resumeRowset->setCreatedConsultantId(1);
		$resumeRowset->setUpdatedConsultantId(1);
		
		$resume = new Default_Model_ResumeMapper();
		$resumeId = $resume->save($resumeRowset);

		$this->_redirect('resume/experience/id/' . $resumeId);
    }
    
    public function experienceAction()
    {
		$resumeId = $this->getRequest()->getParam('id');
		$experienceMapper = new Default_Model_ExperienceMapper();
        $rows = $experienceMapper->fetchAll('resume_id = ' . $resumeId, 'end_date DESC');

		$this->view->resumeId = $resumeId;
		$this->view->rows = $rows;
    }    
    
	public function saveExperienceAction()
    {
		$post = $this->getRequest()->getPost();
///print_r($post);exit;	
		$date = new DateTime($post['startdate']);
        $startDate = $date->format('Y-m-d');

	    $date = new DateTime($post['enddate']);
        $endDate = $date->format('Y-m-d');
        
		$experienceRowset = new Default_Model_Experience();
        $experienceRowset->setResumeId($post['resume_id']);
		$experienceRowset->setStartDate($startDate);
		$experienceRowset->setEndDate($endDate);
		$experienceRowset->setJobTitle($post['job_title']);
		$experienceRowset->setCompanyName($post['company_name']);
		$experienceRowset->setInfo($post['info']);  
		$experienceRowset->setSortOrder(1);   

		$experienceMapper = new Default_Model_ExperienceMapper();
		$experienceMapper->save($experienceRowset);
		
		$this->_redirect('resume/experience/id/' . $post['resume_id']);
    }
}

