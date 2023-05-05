<style type="text/css">

    .image-gallery-wrapper {
      min-height: 500px;
      background-color: rgb(250,250,250);
    }

    @media (max-width: 480px) {
      .image-gallery-wrapper {
        min-height: 260px;
      }
    }
    
    .image-gallery-box {
      position: relative;
      width: 700px;
    }

    .image-gallery-slide {
      opacity: 0;
      min-height: 100px;
      transition: opacity .3s ease-out ;
    }

    /*.image-gallery-slide > .owl-dots {
        margin-top: 20px !important;
    }*/

    /*.image-item-list { width: 48.6%; margin: 1% 0.5%; }

    @media (max-width: 1366px) {
      .image-item-list { width: 48.6%; margin: 1% 0.5%; }
    }

    @media (max-width: 1024px) {
      .image-item-list { width: 48.4%; margin: 1% 0.5%; }
    }

    @media (max-width: 480px) {
      .image-item-list { width: 98%; margin: 2% 1%; }
    }

    @media (max-width: 375px) {
      .image-item-list { width: 99%; margin: 2% 0.5%; }
    }

    .image-item-list {
        margin: 5px;
    }*/

    figure.image-item-list {
        margin: 0;
    }

    .image-item-list img {
      width: 100%;
      vertical-align: top;
      /*box-shadow: 0 1px 3px 0 rgba(0,0,0,.2), 0 1px 1px 0 rgba(0,0,0,.14), 0 2px 1px -1px rgba(0,0,0,.12);*/
    }

    .image-cover {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        top: 0;
        background: linear-gradient(rgba(0,0,0,0) 71%, rgba(0,0,0, .53));
        opacity: 0;
        transition: opacity 200ms;
        border-radius: 12px;
    }

    .image-cover:hover {
        opacity: 1;
    }

    /*.image-gallery {
        position: relative;
    }*/

    .image-gallery > figure {
        /*position: absolute;
        top: 0;
        width: 100%;
        margin: 0;
        z-index: -1;
        opacity: 0;*/

        margin: 0;
        position: absolute;
        height: 100%;
        width: 100%;
        top: 0;
        z-index: -1;
        opacity: 0;
        border-radius: 12px;
    }

    .image-gallery > figure > a {
      width: 100%;
      height: 100%;
      text-align: center;
      display: block;
    }

    .image-gallery > figure > a > img {
      width: 100%;
      height: 100%;
    }

    @media (max-width: 480px) {
      .image-gallery > figure {
        left: 0;
        margin-left: 0;
      }

      .image-gallery-box {
        width: 100%;
      }
    }

    .owl-carousel .owl-stage-outer {
      border-radius: 12px;
    }

    .pswp__img {
      border-radius: 20px;
    }

</style>

  @if(!empty($data['images']))

  <div class="image-gallery-wrapper w-100 mb0 mt4-ns pa2 pa5-ns pb3-ns">

    <div class="image-gallery-box mb4 center relative">

          @if(count($data['images']) == 1)

              <div class="image-gallery image-gallery-slide owl-carousel owl-theme" itemscope>
                  @foreach($data['images'] as $image)
                    <figure class="image-item-list c-border-radius-1 c-shadow-2" itemprop="associatedMedia" itemscope>
                      <a href="{{$image['_url']}}" itemprop="contentUrl" data-size="{{$image['size']}}">
                        <img src="{{$image['_url']}}" itemprop="thumbnail" />
                        <!-- <div class="image-cover"></div> -->
                      </a>
                    </figure>
                  @endforeach
              </div>

          @else

              <div class="image-gallery-slide owl-carousel owl-theme">
                  <?php $ii = 0; ?>
                  @foreach($data['images'] as $image)
                    <figure class="image-item-list c-border-radius-1 c-shadow-2">
                      <a data-toggle="image-gallery" data-image-gallery-slide-target="#image_gallery_{{$ii++}}">
                        <img src="{{$image['_url']}}" />
                        <!-- <div class="image-cover"></div> -->
                      </a>
                    </figure>
                  @endforeach
              </div>

              <div class="image-gallery" itemscope>
                <?php $ii = 0; ?>
                @foreach($data['images'] as $image)
                  <figure itemprop="associatedMedia" itemscope>
                    <a id="image_gallery_{{$ii++}}" href="{{$image['_url']}}" itemprop="contentUrl" data-size="{{$image['size']}}">
                      <img src="{{$image['_preview_url']}}" itemprop="thumbnail" />
                    </a>
                  </figure>
                @endforeach
              </div>

          @endif 

    </div>

  </div>
  @else
  <!-- <div class="container mv7">
    <div class="message-panel tc">
      <div class="center w-90 w-100-ns">
        <h5>ไม่มีรูปภาพสินค้า</h5>
      </div>
    </div>
  </div> -->
  @endif

<!-- Root element of PhotoSwipe. Must have class pswp. -->
<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
  <!-- Background of PhotoSwipe. 
       It's a separate element, as animating opacity is faster than rgba(). -->
  <div class="pswp__bg"></div>

  <!-- Slides wrapper with overflow:hidden. -->
  <div class="pswp__scroll-wrap">

    <!-- Container that holds slides. PhotoSwipe keeps only 3 slides in DOM to save memory. -->
    <!-- don't modify these 3 pswp__item elements, data is added later on. -->
    <div class="pswp__container">
        <div class="pswp__item"></div>
        <div class="pswp__item"></div>
        <div class="pswp__item"></div>
    </div>

    <!-- Default (PhotoSwipeUI_Default) interface on top of sliding area. Can be changed. -->
    <div class="pswp__ui pswp__ui--hidden">

        <div class="pswp__top-bar">

            <!--  Controls are self-explanatory. Order can be changed. -->

            <div class="pswp__counter"></div>

            <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>

            <button class="pswp__button pswp__button--share" title="Share"></button>

            <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>

            <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>

            <!-- element will get class pswp__preloader--active when preloader is running -->
            <div class="pswp__preloader">
                <div class="pswp__preloader__icn">
                  <div class="pswp__preloader__cut">
                    <div class="pswp__preloader__donut"></div>
                  </div>
                </div>
            </div>
        </div>

        <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
            <div class="pswp__share-tooltip"></div> 
        </div>

        <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
        </button>

        <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
        </button>

        <div class="pswp__caption">
            <div class="pswp__caption__center"></div>
        </div>

      </div>

    </div>

</div>

<script type="text/javascript">

    var initPhotoSwipeFromDOM = function(gallerySelector) {

        // parse slide data (url, title, size ...) from DOM elements 
        // (children of gallerySelector)
        var parseThumbnailElements = function(el) {
            var thumbElements = el.childNodes,
                numNodes = thumbElements.length,
                items = [],
                figureEl,
                linkEl,
                size,
                item;

            for(var i = 0; i < numNodes; i++) {

                figureEl = thumbElements[i]; // <figure> element

                // include only element nodes 
                if(figureEl.nodeType !== 1) {
                    continue;
                }

                linkEl = figureEl.children[0]; // <a> element

                size = linkEl.getAttribute('data-size').split('x');

                // create slide object
                item = {
                    src: linkEl.getAttribute('href'),
                    w: parseInt(size[0], 10),
                    h: parseInt(size[1], 10)
                };



                if(figureEl.children.length > 1) {
                    // <figcaption> content
                    item.title = figureEl.children[1].innerHTML; 
                }

                if(linkEl.children.length > 0) {
                    // <img> thumbnail element, retrieving thumbnail url
                    item.msrc = linkEl.children[0].getAttribute('src');
                } 

                item.el = figureEl; // save link to element for getThumbBoundsFn
                items.push(item);
            }

            return items;
        };

        // find nearest parent element
        var closest = function closest(el, fn) {
            return el && ( fn(el) ? el : closest(el.parentNode, fn) );
        };

        // triggers when user clicks on thumbnail
        var onThumbnailsClick = function(e) {
            e = e || window.event;
            e.preventDefault ? e.preventDefault() : e.returnValue = false;

            var eTarget = e.target || e.srcElement;

            // find root element of slide
            var clickedListItem = closest(eTarget, function(el) {
                return (el.tagName && el.tagName.toUpperCase() === 'FIGURE');
            });

            if(!clickedListItem) {
                return;
            }

            // find index of clicked item by looping through all child nodes
            // alternatively, you may define index via data- attribute
            var clickedGallery = clickedListItem.parentNode,
                childNodes = clickedListItem.parentNode.childNodes,
                numChildNodes = childNodes.length,
                nodeIndex = 0,
                index;

            for (var i = 0; i < numChildNodes; i++) {
                if(childNodes[i].nodeType !== 1) { 
                    continue; 
                }

                if(childNodes[i] === clickedListItem) {
                    index = nodeIndex;
                    break;
                }
                nodeIndex++;
            }



            if(index >= 0) {
                // open PhotoSwipe if valid index found
                openPhotoSwipe( index, clickedGallery );
            }
            return false;
        };

        // parse picture index and gallery index from URL (#&pid=1&gid=2)
        var photoswipeParseHash = function() {
            var hash = window.location.hash.substring(1),
            params = {};

            if(hash.length < 5) {
                return params;
            }

            var vars = hash.split('&');
            for (var i = 0; i < vars.length; i++) {
                if(!vars[i]) {
                    continue;
                }
                var pair = vars[i].split('=');  
                if(pair.length < 2) {
                    continue;
                }           
                params[pair[0]] = pair[1];
            }

            if(params.gid) {
                params.gid = parseInt(params.gid, 10);
            }

            return params;
        };

        var openPhotoSwipe = function(index, galleryElement, disableAnimation, fromURL) {
            var pswpElement = document.querySelectorAll('.pswp')[0],
                gallery,
                options,
                items;

            items = parseThumbnailElements(galleryElement);

            // define options (if needed)
            options = {

                fullscreenEl: false,
                // shareEl: false,
                shareButtons: [
                  {id:'facebook', label:'Share on Facebook', url:'https://www.facebook.com/sharer/sharer.php?u={{Request::fullUrl()}}'},
                  {id:'twitter', label:'Tweet', url:'https://twitter.com/intent/tweet?text={{$data['title']}}&url={{Request::fullUrl()}}'},
                  {id:'google-plus', label:'Google Plus', url:'https://plus.google.com/share?url={{Request::fullUrl()}}'},
                ],

                // define gallery index (for URL)
                galleryUID: galleryElement.getAttribute('data-pswp-uid'),

                getThumbBoundsFn: function(index) {
                    // See Options -> getThumbBoundsFn section of documentation for more info
                    var thumbnail = items[index].el.getElementsByTagName('img')[0], // find thumbnail
                        pageYScroll = window.pageYOffset || document.documentElement.scrollTop,
                        rect = thumbnail.getBoundingClientRect(); 

                    return {x:rect.left, y:rect.top + pageYScroll, w:rect.width};
                }

            };

            // PhotoSwipe opened from URL
            if(fromURL) {
                if(options.galleryPIDs) {
                    // parse real index when custom PIDs are used 
                    // http://photoswipe.com/documentation/faq.html#custom-pid-in-url
                    for(var j = 0; j < items.length; j++) {
                        if(items[j].pid == index) {
                            options.index = j;
                            break;
                        }
                    }
                } else {
                    // in URL indexes start from 1
                    options.index = parseInt(index, 10) - 1;
                }
            } else {
                options.index = parseInt(index, 10);
            }

            // exit if index not found
            if( isNaN(options.index) ) {
                return;
            }

            if(disableAnimation) {
                options.showAnimationDuration = 0;
            }

            // Pass data to PhotoSwipe and initialize it
            gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
            gallery.init();
        };

        // loop through all gallery elements and bind events
        var galleryElements = document.querySelectorAll( gallerySelector );

        for(var i = 0, l = galleryElements.length; i < l; i++) {
            galleryElements[i].setAttribute('data-pswp-uid', i+1);
            galleryElements[i].onclick = onThumbnailsClick;
        }

        // Parse URL and open gallery if it contains #&pid=3&gid=1
        var hashData = photoswipeParseHash();
        if(hashData.pid && hashData.gid) {
            openPhotoSwipe( hashData.pid ,  galleryElements[ hashData.gid - 1 ], true, true );
        }
    };

  $(document).ready(function () {

    // execute above function
    initPhotoSwipeFromDOM('.image-gallery');

    setTimeout(function(){
      $('.image-gallery-slide').css('opacity','1');
    },600);


    @if(count($data['images']) == 1)

    $('.image-gallery-slide').owlCarousel({
      loop: false,
      nav: false,
      dots: false,
      margin: 10,
      autoplay:false,
      responsiveClass: true,
      items: 1
    });
    
    @else

    $('.image-gallery-slide').owlCarousel({
      loop: true,
      nav: false,
      dots: true,
      margin: 10,
      autoplay:true,
      autoplayTimeout:4000,
      autoplayHoverPause:true,
      responsiveClass: true,
      items: 1
    });

    $('body').on('click','[data-toggle="image-gallery"]',function(e){
      e.preventDefault();

      $($(this).data('image-gallery-slide-target')).click();
    });
    @endif

  });
</script>