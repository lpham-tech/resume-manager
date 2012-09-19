<?php

class ResumeController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
		$this->view->number = 123456;
		$this->view->name = 'phan duy canh';	
    }
    
	
	public function personalInfoAction()
    {
		//$this->_redirect('resume/experience');
    }
	
	public function saveResumeAction()
    {
    	$post = $this->getRequest()->getPost();
///print_r($post);exit;		
		$resumeRowset = new Default_Model_Resume();
			
		$resumeRowset->setResumeCode('R-01');
		$resumeRowset->setFullName('phan duy canh');
		$resumeRowset->setBirthday('1985-07-25');
		$resumeRowset->setGender('Male');
		$resumeRowset->setMaritaStatus(1);
		$resumeRowset->setStatus(1);
		$resumeRowset->setEmail1('phanduycanha4@mail.com');
		$resumeRowset->setEmail2('phanduycanh@mail.com');
		$resumeRowset->setMobile1('4534654366');
		$resumeRowset->setMobile2('07898765');
		$resumeRowset->setTel('09876543');
		$resumeRowset->setAddress('Lac Long Quan');
		$resumeRowset->setProvinceId(1);
		$resumeRowset->setNationalityId(1);
		$resumeRowset->setViewCount(4);
		$resumeRowset->setCreatedDate('2012-08-08');
		$resumeRowset->setUpdatedDate('2012-08-08');
	
		$resume = new Default_Model_ResumeMapper();
		$resume->save($resumeRowset);
		$this->_redirect('resume/experience');
    }
    
    public function experienceAction()
    {

    }    
    
	public function saveExperienceAction()
    {
		$post = $this->getRequest()->getPost();
///print_r($post);exit;		
		$experienceRowset = new Default_Model_Experience();
        $experienceRowset->setResumeId(1);
		$experienceRowset->setStartDate('2010-10-11');
		$experienceRowset->setEndDate('2011-10-12');
		$experienceRowset->setJobTitle('Manage IT');
		$experienceRowset->setCompanyName('PFT');
		$experienceRowset->setInfo('tettttttttttt');  
		$experienceRowset->setSortOrder(1);   

		$experienceMapper = new Default_Model_ExperienceMapper();
		$experienceMapper->save($experienceRowset);
		$this->_redirect('resume/experience');
    }
}
