LimePHP.register("page.users", function() {

	window.addEventListener("scroll", offsetParallaxBackgrounds, false);

	function offsetParallaxBackgrounds(){
		var parallaxDivs = document.getElementsByClassName('parallax');
		var pageBody = document.getElementById('contentBody');

		var pageBodyTop = pageBody.getBoundingClientRect().top;
		var toShift = (pageBodyTop - 340) * -0.5;

		for (var i = 0; i < parallaxDivs.length; i++){
			var viewportPos = parallaxDivs[i].getBoundingClientRect().top;
			var neededOffset = 400 * (viewportPos/window.innerHeight);

			parallaxDivs[i].style.transform = "translateY(-" + toShift + "px" + ")";
		}

	}


});