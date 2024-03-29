<?php
class CompanyController extends Zend_Controller_Action
{

	public function init()
	{
		$aNamespace = new Zend_Session_Namespace ( 'zs_User' );
		if (! isset ( $aNamespace->islogin )) $this->_redirect ( '/user' );

		/* Initialize action controller here */
		$view = new Zend_View();
		$view->headLink()->appendStylesheet ( '/js/themes/base/jquery.ui.all.css' );
		$view->headScript()->appendFile ( '/js/jquery.ui.datepicker.js' );
		$view->headScript()->appendFile ( '/js/jquery.ui.core.js' );
		$this->view->username = $aNamespace->username;
		$this->view->fullname = $aNamespace->fullname;
		$this->view->isAdmin = $aNamespace->isAdmin;
		date_default_timezone_set('Asia/Ho_Chi_Minh');
	}

	public function checkLoginAction()
	{
		$aNamespace = new Zend_Session_Namespace ( 'zs_User' );
		if (! isset ( $aNamespace->islogin )) echo 0;
		echo 1;
		exit;
	}
	public function indexAction()
	{
		$view = new Zend_View();
		$view->headScript()->appendFile ( '/js/jquery.mousewheel-3.0.6.pack.js' );
		$view->headScript()->appendFile ( '/js/jquery.fancybox.js?v=2.1.0' );
		$view->headLink()->appendStylesheet ( '/js/jquery.fancybox.css?v=2.1.0' ); 
		
		$company = new Default_Model_CompanyMapper();
		$rowPerPage = $this->_getParam('rowperpage', 20);
		$currentPage = 1;
		$page = $this->_getParam('page', 1);
		if(!empty($page)) {
			$currentPage = $page;
		}
		
		$txtSearch = $this->_getParam('txtSearch','');
		$opt = $this->_getParam('opt','company_name');
		$sort = $this->_getParam('sort','created_date');
		$order = $this->_getParam('order','DESC');
		
		$where = '';
		if($txtSearch) $where = $opt .' like "%'. $txtSearch .'%"';
		$order1 = ' ORDER BY ' . $sort . ' ' .$order;
				
		$rows = $company->getListCompany($where, $order1);	
		$paginator = Zend_Paginator::factory($rows);
		$paginator->setItemCountPerPage($rowPerPage);
		$paginator->setCurrentPageNumber($currentPage);

		$this->view->paginator = $paginator;
		$this->view->txtSearch = $txtSearch;
		$this->view->opt = $opt;
		$this->view->sort = $sort;
		$this->view->order = $order;
	}	
	
	public function addCompanyAction()
	{
	    $this->view->title = 'ADD COMPANY';
		$company_id = $this->_getParam('id','');
		$company = array();
		
		if($company_id) {
		    $this->view->title = 'EDIT COMPANY';
		    $companyMapper = new Default_Model_CompanyMapper();
		    $company = $companyMapper->getCompany($company_id);	
		}
		
		$this->view->company = $company;
	}
	
	public function saveCompanyAction()
	{
	    $post = $this->getRequest()->getPost();
	    $contact = new Default_Model_ContactPerson();
	    $company = new Default_Model_Company();
	    
	    $company->setCompanyId($post['company_id']);
		$company->setCompanyName($post['company_name']);
		$company->setBusinesType($post['busines_type']);
		$company->setTel($post['tel']);
		$company->setEmail($post['email']);
		$company->setAddress($post['address']);
		$company->setWebsite($post['website']);
		$company->setInformation($post['information']);
		$company->setAssignCons($post['assign_cons']);
		$company->setCreatedDate(date('Y-m-d'));
		$company->setUpdatedDate(date('Y-m-d'));
		
		$companyMapper = new Default_Model_CompanyMapper();
	    $companyId = $companyMapper->save($company);
	    
		if($companyId){
			$contactperson = new Default_Model_ContactPerson(); 
			$contactperson->setContactPersonId($post['contact_person_id']);
			$contactperson->setCompanyId($companyId); 
			$contactperson->setTitle($post['title']); 
			$contactperson->setFullName($post['full_name']);
			$contactperson->setJobTitle($post['job_title']); 
			$contactperson->setMobile($post['ct_mobile']);
			$contactperson->setEmail($post['ct_email']);
			
			$contactPersonMapper = new Default_Model_ContactPersonMapper();
			$contactPersonMapper->save($contactperson);
		}
		
	    $this->_redirect('/company');
	}
	
	public function viewCompanyAction()
	{
		$this->_helper->layout->disableLayout();
		$company = new Default_Model_Company();
		$companyMapper = new Default_Model_CompanyMapper();
		$company_id = $this->_getParam('company_id','');
		$companyMapper->find($company_id, $company);
		$this->view->company = $company;
	}
	
    public function saveCommentAction()
	{
		$post = $this->getRequest()->getPost();

		$companyMapper = new Default_Model_CompanyMapper();
		$companyMapper->insertComment($post);

		$comment = $companyMapper->getComments($post['company_id'], 1);
		$date = new DateTime($comment['created_date']);
		$createdComment = $date->format('M-d');

		//$html = '<p class="update-date">Comment '. $createdComment .'</p> <span>by</span> <span class="user-name">'. $comment['username'] . '</span>';
		$html = '- ' . substr($comment['content'], 0, 90);
		//$html .= '<a href="#allcomment" class="allcomment" id="'.  $post['company_id'] .'" onClick="allComment('.  $post['company_id'] .')">view all</a>';
		echo $html;
		exit;
	}

	public function deleteCommentAction(){
	    $commentId = $this->_getParam('comment_id','');
	    $companyMapper = new Default_Model_CompanyMapper();
		$companyMapper->deleteComment($commentId);
		exit;
	}
	
	public function allCommentAction()
	{
	    $post = $this->getRequest()->getPost();
		$baseUrl = $this->getFrontController ()->getBaseUrl ();
		
		$companyMapper = new Default_Model_CompanyMapper();
		$comments = $companyMapper->getComments($post['company_id']);

		$html = '';
		$html .= '<div style="color:#1B7CBD;background-color:#CCC;padding:5px 0px"><b>ALL COMMENT</b></div><br />';
		foreach($comments as $comment) {

			$date = new DateTime($comment['created_date']);
			$createdComment = $date->format('M-d');

			$html .= '<div>';
			$html .= substr($comment['content'], 0, 90);
			$html .= '<img class="deletecomment" id="'. $comment['id'] .'" src="'. $baseUrl .'/images/cross.png" alt="delete comment" style="float:right;cursor:pointer;">';
			$html .= '<div align="right"><span>Comment '. $createdComment .'</span> ';
			$html .= '<span style="color: #70B4E2;">by ' . $comment['username'] . '</span></div>';
			$html .= '<div style="border-top: 1px solid #BCBCBC; margin-top:10px">&nbsp;</div>';
			$html .= '</div>';
			
		}
		$html .= '<script language="javascript">
            $(document).ready(function() {
            	$(".deletecomment").click(function(){
            		$(this).parent().hide();
					var comment_id = $(this).attr("id");
                    $.ajax({
                        url: "/company/delete-comment",
                        type: "POST",
                        data: { 
                        	comment_id: comment_id
                        },
                        success: function() {
                            
                        }
                    });
                });   
            });  
        </script>';
		echo $html;
		exit;
	}
	
	public function deleteCompanyAction()
	{
	    $companyMapper = new Default_Model_CompanyMapper();
	    $listid = $this->getRequest()->getParam('listid', 0);
        $companyMapper->deleteListCompany($listid);
	    
		$rows = $companyMapper->getListCompany();	
		$paginator = Zend_Paginator::factory($rows);
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber(1);

		$this->view->paginator = $paginator;
        $this->_helper->layout->disableLayout();
	    $this->render('list-company');
	}
	
	public function companyReportAction()
	{
		$this->_helper->layout->disableLayout();

		$companyMapper = new Default_Model_CompanyMapper();
		$countNew = $companyMapper->countCompanyWith('new');
		$countUpdate = $companyMapper->countCompanyWith('update');
		$countTotal = $companyMapper->countCompanyWith('total');

		$html = 'Today Entry: <strong>New '. $countNew .'</strong> | ';
		$html .= 'Updated: <strong>'. $countUpdate .'</strong$countNew> | ';
		$html .= 'Total: <strong>'. $countTotal .'</strong>';
		echo $html;
		exit;
	}
}
