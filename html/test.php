<!DOCTYPE html>
<html lang=&quot;en&quot; >

<head>

  <meta charset=&quot;UTF-8&quot;>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  
<link rel=&quot;apple-touch-icon&quot; type=&quot;image/png&quot; href=&quot;https://cpwebassets.codepen.io/assets/favicon/apple-touch-icon-5ae1a0698dcc2402e9712f7d01ed509a57814f994c660df9f7a952f3060705ee.png&quot; />
<meta name=&quot;apple-mobile-web-app-title&quot; content=&quot;CodePen&quot;>

<link rel=&quot;shortcut icon&quot; type=&quot;image/x-icon&quot; href=&quot;https://cpwebassets.codepen.io/assets/favicon/favicon-aec34940fbc1a6e787974dcd360f2c6b63348d4b1f4e06c77743096d55480f33.ico&quot; />

<link rel=&quot;mask-icon&quot; type=&quot;image/x-icon&quot; href=&quot;https://cpwebassets.codepen.io/assets/favicon/logo-pin-8f3771b1072e3c38bd662872f6b673a722f4b3ca2421637d5596661b4e2132cc.svg&quot; color=&quot;#111&quot; />


  <title>CodePen - jQuery Organic Tabs</title>
  
  
  
  
<style>
/* Specific to example one */

#example-one {
  background: #eee;
  padding: 10px;
  margin: 0 0 20px 0;
  box-shadow: 0 0 5px #666;
}

#example-one .nav {
  overflow: hidden;
  margin: 0 0 10px 0;
}
#example-one .nav li {
  width: 97px;
  float: left;
  margin: 0 10px 0 0;
}
#example-one .nav li.last {
  margin-right: 0;
}
#example-one .nav li a {
  display: block;
  padding: 5px;
  background: #959290;
  color: white;
  font-size: 10px;
  text-align: center;
  border: 0;
}
#example-one .nav li a:hover {
  background-color: #111;
}

#example-one ul {
  list-style: none;
}
#example-one ul li a {
  display: block;
  border-bottom: 1px solid #666;
  padding: 4px;
  color: #666;
}
#example-one ul li a:hover {
  background: #fe4902;
  color: white;
}
#example-one ul li:last-child a {
  border: none;
}

#example-one ul li.nav-one a.current,
#example-one ul.featured li a:hover {
  background-color: #0575f4;
  color: white;
}
#example-one ul li.nav-two a.current,
#example-one ul.core li a:hover {
  background-color: #d30000;
  color: white;
}
#example-one ul li.nav-three a.current,
#example-one ul.jquerytuts li a:hover {
  background-color: #8d01b0;
  color: white;
}
#example-one ul li.nav-four a.current,
#example-one ul.classics li a:hover {
  background-color: #fe4902;
  color: white;
}

/* Specific to example two */

#example-two .list-wrap {
  background: #eee;
  padding: 10px;
  margin: 0 0 15px 0;
}

#example-two ul {
  list-style: none;
}
#example-two ul li a {
  display: block;
  border-bottom: 1px solid #666;
  padding: 4px;
  color: #666;
}
#example-two ul li a:hover {
  background: #333;
  color: white;
}
#example-two ul li:last-child a {
  border: none;
}

#example-two .nav {
  overflow: hidden;
}
#example-two .nav li {
  width: 97px;
  float: left;
  margin: 0 10px 0 0;
}
#example-two .nav li.last {
  margin-right: 0;
}
#example-two .nav li a {
  display: block;
  padding: 5px;
  background: #666;
  color: white;
  font-size: 10px;
  text-align: center;
  border: 0;
}

#example-two li a.current,
#example-two li a.current:hover {
  background-color: #eee !important;
  color: black;
}
#example-two .nav li a:hover,
#example-two .nav li a:focus {
  background: #999;
}

/* Generic Utility */
.hide {
  position: absolute;
  top: -9999px;
  left: -9999px;
}
* {
  margin: 0;
  padding: 0;
}
body {
  font: 12px Georgia, serif;
  margin: 1rem;
}
html {
  overflow-y: scroll;
}
a {
  text-decoration: none;
}
a:focus {
  outline: 0;
}
p {
  font-size: 15px;
  margin: 0 0 20px 0;
}
#page-wrap {
  width: 440px;
  margin: 80px auto;
}
h1 {
  font: bold 40px Sans-Serif;
  margin: 0 0 20px 0;
}
</style>

  
  
  
  <script>
  if (document.location.search.match(/type=embed/gi)) {
    window.parent.postMessage(&quot;resize&quot;, &quot;*&quot;);
  }
</script>


</head>

<body translate=&quot;no&quot; >
  <div id=&quot;example-one&quot;>
  <ul class=&quot;nav&quot;>
    <li class=&quot;nav-one&quot;><a href=&quot;#featured&quot; class=&quot;current&quot;>Featured</a></li>
    <li class=&quot;nav-two&quot;><a href=&quot;#core&quot;>Core</a></li>
    <li class=&quot;nav-three&quot;><a href=&quot;#jquerytuts&quot;>jQuery</a></li>
    <li class=&quot;nav-four last&quot;><a href=&quot;#classics&quot;>Classics</a></li>
  </ul>
  <div class=&quot;list-wrap&quot;>
    <ul id=&quot;featured&quot;>
      <li><a href=&quot;http://css-tricks.com/perfect-full-page-background-image/&quot;>Full Page Background Images</a></li>
      <li><a href=&quot;http://css-tricks.com/designing-for-wordpress-complete-series-downloads/&quot;>Designing for WordPress</a></li>
      <li><a href=&quot;http://css-tricks.com/build-your-own-social-home/&quot;>Build Your Own Social Home!</a></li>
      <li><a href=&quot;http://css-tricks.com/absolute-positioning-inside-relative-positioning/&quot;>Absolute Positioning Inside
Relative Positioning</a></li>
      <li><a href=&quot;http://css-tricks.com/ie-css-bugs-thatll-get-you-every-time/&quot;>IE CSS Bugs That'll Get You Every Time</a></li>
      <li><a href=&quot;http://css-tricks.com/404-best-practices/&quot;>404 Best Practices</a></li>
      <li><a href=&quot;http://css-tricks.com/date-display-with-sprites/&quot;>Date Display with Sprites</a></li>
    </ul>
    <ul id=&quot;core&quot; class=&quot;hide&quot;>
      <li><a href=&quot;http://css-tricks.com/video-screencasts/58-html-css-the-very-basics/&quot;>The VERY Basics of HTML &amp;amp;
CSS</a></li>
      <li><a href=&quot;http://css-tricks.com/the-difference-between-id-and-class/&quot;>Classes and IDs</a></li>
      <li><a href=&quot;http://css-tricks.com/the-css-box-model/&quot;>The CSS Box Model</a></li>
      <li><a href=&quot;http://css-tricks.com/all-about-floats/&quot;>All About Floats</a></li>
      <li><a href=&quot;http://css-tricks.com/the-css-overflow-property/&quot;>CSS Overflow Property</a></li>
      <li><a href=&quot;http://css-tricks.com/css-font-size/&quot;>CSS Font Size - (px - em - % - pt - keyword)</a></li>
      <li><a href=&quot;http://css-tricks.com/css-transparency-settings-for-all-broswers/&quot;>CSS Transparency / Opacity</a></li>
      <li><a href=&quot;http://css-tricks.com/css-sprites/&quot;>CSS Sprites</a></li>
      <li><a href=&quot;http://css-tricks.com/nine-techniques-for-css-image-replacement/&quot;>CSS Image Replacement</a></li>
      <li><a href=&quot;http://css-tricks.com/what-is-vertical-align/&quot;>CSS Vertial Align</a></li>
      <li><a href=&quot;http://css-tricks.com/the-css-overflow-property/&quot;>The CSS Overflow Property</a></li>
    </ul>
    <ul id=&quot;jquerytuts&quot; class=&quot;hide&quot;>
      <li><a href=&quot;http://css-tricks.com/anythingslider-jquery-plugin/&quot;>Anything Slider jQuery Plugin</a></li>
      <li><a href=&quot;http://css-tricks.com/moving-boxes/&quot;>Moving Boxes</a></li>
      <li><a href=&quot;http://css-tricks.com/simple-jquery-dropdowns/&quot;>Simple jQuery Dropdowns</a></li>
      <li><a href=&quot;http://css-tricks.com/creating-a-slick-auto-playing-featured-content-slider/&quot;>Featured Content Slider</a></li>
      <li><a href=&quot;http://css-tricks.com/startstop-slider/&quot;>Start/Stop Slider</a></li>
      <li><a href=&quot;http://css-tricks.com/banner-code-displayer-thing/&quot;>Banner Code Displayer Thing</a></li>
      <li><a href=&quot;http://css-tricks.com/highlight-certain-number-of-characters/&quot;>Highlight Certain Number of Characters</a></li>
      <li><a href=&quot;http://css-tricks.com/auto-moving-parallax-background/&quot;>Auto-Moving Parallax Background</a></li>
    </ul>
    <ul id=&quot;classics&quot; class=&quot;hide&quot;>
      <li><a href=&quot;http://css-tricks.com/css-wishlist/&quot;>Top Designers CSS Wishlist</a></li>
      <li><a href=&quot;http://css-tricks.com/what-beautiful-html-code-looks-like/&quot;>What Beautiful HTML Code Looks Like</a></li>
      <li><a href=&quot;http://css-tricks.com/easily-password-protect-a-website-or-subdirectory/&quot;>Easily Password Protect a
Website or Subdirectory</a></li>
      <li><a href=&quot;http://css-tricks.com/how-to-create-an-ie-only-stylesheet/&quot;>IE-Only Stylesheets</a></li>
      <li><a href=&quot;http://css-tricks.com/ecommerce-considerations/&quot;>eCommerce Considerations</a></li>
      <li><a href=&quot;http://css-tricks.com/php-for-beginners-building-your-first-simple-cms/&quot;>PHP: Build Your First CMS</a></li>
    </ul>
  </div>
</div>
<p>This is a plugin, so you can call it on multiple tabbed areas, which can be styled totally differently</p>
<div id=&quot;example-two&quot;>
  <ul class=&quot;nav&quot;>
    <li class=&quot;nav-one&quot;><a href=&quot;#featured2&quot; class=&quot;current&quot;>Featured</a></li>
    <li class=&quot;nav-two&quot;><a href=&quot;#core2&quot;>Core</a></li>
    <li class=&quot;nav-three&quot;><a href=&quot;#jquerytuts2&quot;>jQuery</a></li>
    <li class=&quot;nav-four last&quot;><a href=&quot;#classics2&quot;>Classics</a></li>
  </ul>
  <div class=&quot;list-wrap&quot;>
    <ul id=&quot;featured2&quot;>
      <li><a href=&quot;http://css-tricks.com/perfect-full-page-background-image/&quot;>Full Page Background Images</a></li>
      <li><a href=&quot;http://css-tricks.com/designing-for-wordpress-complete-series-downloads/&quot;>Designing for WordPress</a></li>
      <li><a href=&quot;http://css-tricks.com/build-your-own-social-home/&quot;>Build Your Own Social Home!</a></li>
      <li><a href=&quot;http://css-tricks.com/absolute-positioning-inside-relative-positioning/&quot;>Absolute Positioning Inside
Relative Positioning</a></li>
      <li><a href=&quot;http://css-tricks.com/ie-css-bugs-thatll-get-you-every-time/&quot;>IE CSS Bugs That'll Get You Every Time</a></li>
      <li><a href=&quot;http://css-tricks.com/404-best-practices/&quot;>404 Best Practices</a></li>
      <li><a href=&quot;http://css-tricks.com/date-display-with-sprites/&quot;>Date Display with Sprites</a></li>
    </ul>
    <ul id=&quot;core2&quot; class=&quot;hide&quot;>
      <li><a href=&quot;http://css-tricks.com/video-screencasts/58-html-css-the-very-basics/&quot;>The VERY Basics of HTML &amp;amp;
CSS</a></li>
      <li><a href=&quot;http://css-tricks.com/the-difference-between-id-and-class/&quot;>Classes and IDs</a></li>
      <li><a href=&quot;http://css-tricks.com/the-css-box-model/&quot;>The CSS Box Model</a></li>
      <li><a href=&quot;http://css-tricks.com/all-about-floats/&quot;>All About Floats</a></li>
      <li><a href=&quot;http://css-tricks.com/the-css-overflow-property/&quot;>CSS Overflow Property</a></li>
      <li><a href=&quot;http://css-tricks.com/css-font-size/&quot;>CSS Font Size - (px - em - % - pt - keyword)</a></li>
      <li><a href=&quot;http://css-tricks.com/css-transparency-settings-for-all-broswers/&quot;>CSS Transparency / Opacity</a></li>
      <li><a href=&quot;http://css-tricks.com/css-sprites/&quot;>CSS Sprites</a></li>
      <li><a href=&quot;http://css-tricks.com/nine-techniques-for-css-image-replacement/&quot;>CSS Image Replacement</a></li>
      <li><a href=&quot;http://css-tricks.com/what-is-vertical-align/&quot;>CSS Vertial Align</a></li>
      <li><a href=&quot;http://css-tricks.com/the-css-overflow-property/&quot;>The CSS Overflow Property</a></li>
    </ul>
    <ul id=&quot;jquerytuts2&quot; class=&quot;hide&quot;>
      <li><a href=&quot;http://css-tricks.com/anythingslider-jquery-plugin/&quot;>Anything Slider jQuery Plugin</a></li>
      <li><a href=&quot;http://css-tricks.com/moving-boxes/&quot;>Moving Boxes</a></li>
      <li><a href=&quot;http://css-tricks.com/simple-jquery-dropdowns/&quot;>Simple jQuery Dropdowns</a></li>
      <li><a href=&quot;http://css-tricks.com/creating-a-slick-auto-playing-featured-content-slider/&quot;>Featured Content Slider</a></li>
      <li><a href=&quot;http://css-tricks.com/startstop-slider/&quot;>Start/Stop Slider</a></li>
      <li><a href=&quot;http://css-tricks.com/banner-code-displayer-thing/&quot;>Banner Code Displayer Thing</a></li>
      <li><a href=&quot;http://css-tricks.com/highlight-certain-number-of-characters/&quot;>Highlight Certain Number of Characters</a></li>
      <li><a href=&quot;http://css-tricks.com/auto-moving-parallax-background/&quot;>Auto-Moving Parallax Background</a></li>
    </ul>
    <ul id=&quot;classics2&quot; class=&quot;hide&quot;>
      <li><a href=&quot;http://css-tricks.com/css-wishlist/&quot;>Top Designers CSS Wishlist</a></li>
      <li><a href=&quot;http://css-tricks.com/what-beautiful-html-code-looks-like/&quot;>What Beautiful HTML Code Looks Like</a></li>
      <li><a href=&quot;http://css-tricks.com/easily-password-protect-a-website-or-subdirectory/&quot;>Easily Password Protect a
Website or Subdirectory</a></li>
      <li><a href=&quot;http://css-tricks.com/how-to-create-an-ie-only-stylesheet/&quot;>IE-Only Stylesheets</a></li>
      <li><a href=&quot;http://css-tricks.com/ecommerce-considerations/&quot;>eCommerce Considerations</a></li>
      <li><a href=&quot;http://css-tricks.com/php-for-beginners-building-your-first-simple-cms/&quot;>PHP: Build Your First CMS</a></li>
    </ul>
  </div>
</div>
<p>This is some content below the tabs. I will be moved up or down to accommodate the tabbed area above me.</p>
    <script src=&quot;https://cpwebassets.codepen.io/assets/common/stopExecutionOnTimeout-1b93190375e9ccc259df3a57c1abc0e64599724ae30d7ea4c6877eb615f89387.js&quot;></script>

  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js'></script>
      <script id=&quot;rendered-js&quot; >
(function ($) {
  $.organicTabs = function (el, options) {
    var base = this;
    base.$el = $(el);
    base.$nav = base.$el.find(&quot;.nav&quot;);

    base.init = function () {
      base.options = $.extend({}, $.organicTabs.defaultOptions, options);

      // Accessible hiding fix
      $(&quot;.hide&quot;).css({
        position: &quot;relative&quot;,
        top: 0,
        left: 0,
        display: &quot;none&quot; });


      base.$nav.on(&quot;click&quot;, &quot;li > a&quot;, function () {
        // Figure out current list via CSS class
        var curList = base.$el.
        find(&quot;a.current&quot;).
        attr(&quot;href&quot;).
        substring(1),
        // List moving to
        $newList = $(this),
        // Figure out ID of new list
        listID = $newList.attr(&quot;href&quot;).substring(1),
        // Set outer wrapper height to (static) height of current inner list
        $allListWrap = base.$el.find(&quot;.list-wrap&quot;),
        curListHeight = $allListWrap.height();
        $allListWrap.height(curListHeight);

        if (listID != curList &amp;&amp; base.$el.find(&quot;:animated&quot;).length == 0) {
          // Fade out current list
          base.$el.find(&quot;#&quot; + curList).fadeOut(base.options.speed, function () {
            // Fade in new list on callback
            base.$el.find(&quot;#&quot; + listID).fadeIn(base.options.speed);

            // Adjust outer wrapper to fit new list snuggly
            var newHeight = base.$el.find(&quot;#&quot; + listID).height();
            $allListWrap.animate({
              height: newHeight });


            // Remove highlighting - Add to just-clicked tab
            base.$el.find(&quot;.nav li a&quot;).removeClass(&quot;current&quot;);
            $newList.addClass(&quot;current&quot;);
          });
        }

        // Don't behave like a regular link
        // Stop propegation and bubbling
        return false;
      });
    };
    base.init();
  };

  $.organicTabs.defaultOptions = {
    speed: 300 };


  $.fn.organicTabs = function (options) {
    return this.each(function () {
      new $.organicTabs(this, options);
    });
  };
})(jQuery);

$(&quot;#example-one&quot;).organicTabs();

$(&quot;#example-two&quot;).organicTabs({
  speed: 200 });
//# sourceURL=pen.js
    </script>

  

  <script src=&quot;https://cpwebassets.codepen.io/assets/editor/iframe/iframeRefreshCSS-4793b73c6332f7f14a9b6bba5d5e62748e9d1bd0b5c52d7af6376f3d1c625d7e.js&quot;></script>
</body>

</html>
 
" sandbox="allow-downloads allow-forms allow-modals allow-pointer-lock allow-popups allow-presentation  allow-scripts allow-top-navigation-by-user-activation" allow="accelerometer; camera; encrypted-media; display-capture; geolocation; gyroscope; microphone; midi; clipboard-read; clipboard-write; web-share" allowtransparency="true" allowpaymentrequest="true" allowfullscreen="true" class="result-iframe">
