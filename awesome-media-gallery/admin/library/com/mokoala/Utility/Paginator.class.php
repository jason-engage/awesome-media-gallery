<?php

class MK_Paginator
{

	protected $page;
	protected $per_page;
	protected $total_records = 0;

	public function __construct( $page = 1, $per_page = 10 )
	{
		$this->page = $page;
		$this->per_page = $per_page;
	}

	public function setPage( $page )
	{
		$this->page = $page;
		return $this;
	}

	public function setPerPage( $per_page )
	{
		$this->per_page = $per_page;
		return $this;
	}

	public function setTotalRecords( $total_records )
	{
		$this->total_records = $total_records;
		return $this;
	}

	public function getPage()
	{
		return $this->page;
	}

	public function getTotalPages()
	{
		$total_pages = ceil($this->getTotalRecords() / $this->getPerPage());
		return $total_pages ? $total_pages : 1;
	}

	public function getPerPage()
	{
		return $this->per_page;
		print $this->per_page;
	}

	public function getTotalRecords()
	{
		return $this->total_records;
	}

	public function getRecordStart()
	{
		return $this->per_page * ($this->page - 1);
	}

	public function getFirstRecord()
	{
		return $this->getRecordStart() + 1;
	}

	public function getLastRecord()
	{
		return $this->getFirstRecord() + $this->getPerPage() > $this->getTotalRecords() ? $this->getTotalRecords() : $this->getFirstRecord() + $this->getPerPage();
	}

	public function render($link, $options = array())
	{

		$link = urldecode( $link );

		$default_options = array(
			'paging_range' => 4,
			'next_previous_link' => true,
			'prev_character' => '&lsaquo;',
			'next_character' => '&rsaquo;',
			'first_last_link' => true,
			'first_character' => '&laquo;',
			'last_character' => '&raquo;',
		);

		$options = array_merge_replace($default_options, $options);

		$page_list = array();

		if($this->getTotalPages() > 1)
		{

			$first_display_page = $this->getPage() - $options['paging_range'];
			$first_display_page = $first_display_page < 1 ? 1 : $first_display_page;

			$last_display_page = $this->getPage() + $options['paging_range'];
			$last_display_page = $last_display_page > $this->getTotalPages() ? $this->getTotalPages() : $last_display_page;

			for($p = $first_display_page; $p <= $last_display_page; $p++)
			{
				$page_data = array(
					'page' => $p,
					'text' => $p,
					'link' => ''
				);

				$page_data['link'] = str_replace('{page}', $page_data['page'], $link);

				$page_list[] = $page_data;

			}


		}

		$pages = '<p class="text">Page '.number_format($this->getPage()).' of '.number_format($this->getTotalPages()).'</p>';

		if(count($page_list) > 0)
		{
			$pages .= '<ul class="list">';

			// First
			if( $options['first_last_link'] === true && 1 < $this->getPage() )
			{
				$pages .= '<li rel="paginator first"><span><a href="'.str_replace('{page}', 1, $link).'">'.$options['first_character'].'</a></span></li>';
			}

			// Prev
			if( $options['next_previous_link'] && ( $this->getPage() > 1 ) )
			{
				$pages .= '<li rel="paginator prev"><span><a href="'.str_replace('{page}', $this->getPage() - 1, $link).'">'.$options['prev_character'].'</a></span></li>';
			}

			// Pages
			foreach($page_list as $single_page)
			{
				$pages .= '<li rel="paginator page" class="'.($single_page['page'] == $this->getPage() ? ' selected' : false ).'"><span><a href="'.urldecode( $single_page['link'] ).'">'.$single_page['text'].'</a></span></li>';
			}

			// Next
			if( $options['next_previous_link'] && ( $this->getPage() < $this->getTotalPages() ) )
			{
				$pages .= '<li rel="paginator next"><span><a href="'.str_replace('{page}', $this->getPage() + 1, $link).'">'.$options['next_character'].'</a></span></li>';
			}

			// Last
			if($options['first_last_link'] === true && $this->getTotalPages() > $this->getPage())
			{
				$pages .= '<li rel="paginator last"><span><a href="'.str_replace('{page}', $this->getTotalPages(), $link).'">'.$options['last_character'].'</a></span></li>';
			}

			$pages .= '</ul>';
		}


		return $pages;
	}

}

?>
