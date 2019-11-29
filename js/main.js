$(document).ready(function () {


	//slide2id - плавная прокрутка по ссылкам внутри страницы
	$("nav a footer a,a[href='#top'],a[rel='m_PageScroll2id'],a.PageScroll2id").mPageScroll2id({
		highlightSelector: "nav a"
	});



	// MixItUp - фильтрация
	$('#mixitup').mixItUp();


	// FancyBox - galery
	$(".fancybox").fancybox({
		// Default - with fix from scroll to top
		helpers: {
			overlay: {
				locked: false
			}
		}
	});
	// End of FancyBox - galery


	AOS.init();

}); 