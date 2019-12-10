$(document).ready(function () {


	//slide2id - плавная прокрутка по ссылкам внутри страницы
	$("a,a[href='#wematcher_widget'],a[rel='m_PageScroll2id'],a.PageScroll2id").mPageScroll2id({
		highlightSelector: "a"
	});


	AOS.init();

}); 