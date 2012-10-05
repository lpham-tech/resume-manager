<?php

class Company_IndexController extends Zend_Controller_Action
{

	public function init()
	{
		$view = new Zend_View();
        $view->headScript()->appendFile ( '/js/jquery-1.7.1.js' );
        $view->headLink()->appendStylesheet ( '/js/themes/base/jquery.ui.all.css' );
        $view->headScript()->appendFile ( '/js/jquery.ui.datepicker.js' );
        $view->headScript()->appendFile ( '/js/jquery.ui.core.js' );
	}

	public function indexAction()
	{
		$post = $this->getRequest()->getPost();
		$currentPage = 1;
        $page = $this->_getParam('page',1);
        if(!empty($page)) {
            $currentPage = $page;
        }
		$condition="1";
		if (isset($post['txtSearch']) && trim($post['txtSearch'])!="")
		{
			if ($post['ccode_id']==1)
				$condition = "company_code LIKE '%".$post['txtSearch']."%'";
			else 
			if ($post['ccode_id']==2)
				$condition = "full_name_en LIKE '%".$post['txtSearch']."%' OR short_name_en LIKE '%".$post['txtSearch']."%' OR full_name_vn LIKE '%".$post['txtSearch']."%' OR short_name_vn LIKE '%".$post['txtSearch']."%'";
		}
		
		$order_by="full_name_en ASC";
		if (isset($post['sort_name_id']))
			$order_by = $post['sort_name_id']." ".$post['sort_type_id'];
		$company = new Company_Model_CompanyMapper();
		$data="";

		$list = $company->getListCompany($condition,$order_by,($currentPage-1)*1,1);
		$rows = $company->getListCompany($condition,$order_by);
		$totalRecord=$company->countCompany($condition,$order_by);
		/*if ($totalRecord%1)
			$pageRange=$totalRecord/1;
		else */
		$pageRange=3;
		$paginator = Zend_Paginator::factory($rows);
		$paginator->setItemCountPerPage(1);
		$paginator->setCurrentPageNumber($currentPage);
		$paginator->setPageRange($pageRange);
		foreach($list as $rs)
		{
			$industry_name = $company->getFieldValue("industry_lookup","industry_id='".$rs['industry_id']."'","abbreviation");
			$data .= '<tr>
					  <td class="a-center"><input type="checkbox" name="company_id" id="company_id" value="'.$rs['company_id'].'" /></td>
					  <td class="a-left">'.$rs['company_code'].'</td>
					  <td class="a-left"><a href="#">'.$rs['full_name_en'].'</a></td>
					  <td class="a-left">'.$industry_name.'</td>
					  <td class="a-center color_bg">23</td>
					  <td class="a-center color_bg">23</td>
					  <td class="a-center color_bg">23</td>
					  <td class="a-center color_bg">23</td>
					  <td class="a-center color_bg">23</td>
					  <td class="a-center color_bg">23</td>
					  <td class="a-center color_bg">23</td>
					  <td class="a-center color_bg">23</td>
					  <td class="a-center color_bg">23</td>
					  <td>&nbsp;</td>
					  <td></td>
					  <td><a href="#"><img src="/images/icons/user.png" title="Show profile" width="16" height="16" /></a><a href="#"><img src="/images/icons/user_edit.png" title="Edit user" width="16" height="16" /></a></td>
					</tr>';
		}
		
		$this->view->paginator = $paginator;
		$this->view->rows = $list;
		$this->view->data = $data;
	}
}

