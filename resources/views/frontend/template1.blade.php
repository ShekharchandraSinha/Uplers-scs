<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ $portfolio->name }}</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <link rel="shortcut icon" href="{{ asset('frontend/template1/images/favicon.png') }}" type="image/x-icon" />
    <link rel="icon" href="{{ asset('frontend/template1/images/favicon.png') }}" type="image/x-icon" />
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet" />
    <!--css styles starts-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/template1/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/template1/css/responsive.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/drag.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!--css styles ends-->
</head>

<body class="eu" x-data="layoutManager()" x-init="() => { initLayoutManager(); }">
    @auth
    @if (!$hiddenLayout || !$hiddenPdf)
    <!-- Edit button -->
    <div class="edit-button-container">
        <div class="flex-column">
            @if (!$hiddenPdf)
            <a href="{{ route('public.portfolio.download-pdf', ['portfolioSlug' => $portfolioSlug, 'pageHeight' => '::JSCALCULATEDHEIGHT::']) }}" id="download-pdf" class="rounded-circle btn btn-primary" target="_blank"><i class="fa fa-download"></i></a>
            @endif
            @if (!$hiddenLayout)
            <template x-if="!isInEditMode">
                <button class="rounded-circle btn btn-primary" @click="enableDragAndDrop()"><i class="fa fa-edit"></i></button>
            </template>
            <template x-if="isInEditMode">
                <button type="button" class="rounded-circle btn btn-success" @click="saveChanges()"><i class="fa fa-save"></i></button>
                <button type="button" class="rounded-circle btn btn-danger" @click="disableDragAndDrop()"><i class="fa fa-times"></i></button>
            </template>
            @endif
        </div>
    </div>
    <!-- /edit button -->
    @endif
    @endauth
    <div class="wrapper flw">
        <!--header starts here-->
        <header class="flw banner-section" style="background-image: url('{{ asset('frontend/template1/images/bg-img.png') }}')">
            <div class="logo-section">
                <a href="https://www.uplers.com/"><img src="{{ asset('frontend/template1/images/uplers-logo.png') }}" title="" alt="" /></a>
            </div>
            <div class="main">
                @php
                if(isset($portfolio->profile_photo)){
                $image =$portfolio->profile_photo;
                } else {

                $image = 'dummy/image-placeholder.png';
                }
                @endphp
                <div class="emp_details">
                    <img src="{{ asset('img/portfolio-profile/' . $image) }}" title="" alt="" class="emp_logo" />
                    <h1>{{ $portfolio->name }}</h1>
                    <div class="emp_title">{{ $portfolio->designation }}</div>
                </div>
            </div>
        </header>
        <!--header ends here-->

        <!--mid container starts here-->
        <div class="mid-container flw">
            <template x-for="(listingItem, index) in layout" :key="index">
                <div>
                    @if (isset($sectionData['profile']))
                    <template x-if="listingItem == 'profile'">
                        <section class="section-1 section pdf-section animated-highlight" @auth :class="{'drag': isInEditMode}" @endauth id="profile">
                            <div class="main">
                                <div class="black-box">
                                    <div class="profile-header c-white">{{ strtoupper($sectionData['profile']->label) }}</div>
                                    <div class="profile-desc">{{ $sectionData['profile']->model }}</div>
                                </div>
                            </div>
                        </section>
                    </template>
                    @endif
                    @if (isset($sectionData['fact']) && $sectionData['fact']->display)
                    <template x-if="listingItem == 'fact'">
                        <section class="section-1 section pdf-section animated-highlight" @auth :class="{'drag': isInEditMode}" @endauth id="fact">
                            <div class="main">
                                <div class="fun-fact-block">
                                    <div class="yellow-box">
                                        <div class="profile-header c-white">{{ strtoupper($sectionData['fact']->label) }}</div>
                                        <div class="profile-desc">{{ $sectionData['fact']->model }}</div>
                                    </div>
                                    <div class="ex-mark"><img src="{{ asset('frontend/template1/images/exclamation-mark.png') }}" /></div>
                                </div>
                            </div>
                        </section>
                    </template>
                    @endif
                    @if(isset($sectionData['experience']) && $sectionData['experience']->display)
                    <template x-if="listingItem == 'experience'">
                        <section class="section-1 section pdf-section animated-highlight" @auth :class="{'drag': isInEditMode}" @endauth id="experience">
                            <div class="main">
                                <div class="profile-header c-blk br-grey">{{ strtoupper($sectionData['experience']->label) }}</div>
                                <div class="profile-desc">
                                    <ul>
                                        @foreach ($sectionData['experience']->model as $model)
                                        <li>
                                            <h4>{{ $model->title }} at {{ $model->company }} ({{ $model->start }} - {{ $model->end }})</h4>
                                            <ul class="dashed">
                                                @foreach ($model->content as $content)
                                                <li>{{ $content->model }}</li>
                                                @endforeach
                                            </ul>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </section>
                    </template>
                    @endif
                    @if(isset($sectionData['education']))
                    <template x-if="listingItem == 'education'">
                        <section class="section-1 section pdf-section animated-highlight" @auth :class="{'drag': isInEditMode}" @endauth id="education">
                            <div class="main">
                                <div>
                                    <div class="profile-header c-blk br-grey">{{ strtoupper($sectionData['education']->label) }}</div>
                                    <div class="profile-desc">
                                        <ul>
                                            @foreach ($sectionData['education']->model as $model)
                                            <li>
                                                <h4>{{ $model->course }} at {{ $model->institution }} ({{ $model->start }} - {{ $model->end }})</h4>
                                                @if ($model->content != "")
                                                <p style="margin-bottom: 0;">{{ $model->content }}</p>
                                                @endif
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </template>
                    @endif
                    @if(isset($sectionData['testimonial']) && $sectionData['testimonial']->display)
                    <template x-if="listingItem == 'testimonial'">
                        <section class="section-1 section pdf-section animated-highlight" @auth :class="{'drag': isInEditMode}" @endauth id="testimonial">
                            <div class="main">
                                <div class="testimonial">
                                    <div class="title">{{ strtoupper($sectionData['testimonial']->label) }}</div>
                                    <div class="testimonial-quote"><img src="{{ asset('frontend/template1/images/double-quote.png') }}" /></div>
                                    <div class="quote">{{ $sectionData['testimonial']->model }}</div>
                                </div>
                            </div>
                        </section>
                    </template>
                    @endif
                    @if (isset($sectionData['achievements']) && $sectionData['achievements']->display)
                    <template x-if="listingItem == 'achievements'">
                        <div class="section-2 section pdf-section animated-highlight" @auth :class="{'drag': isInEditMode}" @endauth id="achievements">
                            <div class="main">
                                <div class="profile-header c-blk br-grey">{{ strtoupper($sectionData['achievements']->label) }}</div>
                                <div class="profile-desc">
                                    <ul>
                                        @foreach ($sectionData['achievements']->model as $content)
                                        <li>{{ $content->model }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </template>
                    @endif
                    @if(isset($sectionData['qualities']) && $sectionData['qualities']->display && sizeof($sectionData['qualities']->model) > 0)
                    <template x-if="listingItem == 'qualities'">
                        <div class="section-2 section pdf-section animated-highlight" @auth :class="{'drag': isInEditMode}" @endauth id="qualities">
                            <div class="main">
                                <div class="profile-header c-blk br-grey">{{ strtoupper($sectionData['qualities']->label) }}</div>
                                <div class="flex-container pd-t30">
                                    @foreach ($sectionData['qualities']->model as $item)
                                    <div class="block pad-right key-skill-pie-chart">
                                        <div class="chart easyPieChart" data-percent="{{ $item->rating * 10 }}" style="width: 200px; height: 200px; line-height: 200px;">
                                            <div class="chart-inner">{{ $item->title }}</div>
                                            <canvas width="300" height="300" style="width: 200px; height: 200px;"></canvas>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </template>
                    @endif
                    @if(isset($sectionData['clientele']) && $sectionData['clientele']->display)
                    <template x-if="listingItem == 'clientele'">
                        <div class="section-2 section pdf-section animated-highlight" @auth :class="{'drag': isInEditMode}" @endauth id="clientele">
                            <div class="main">
                                <h5 class="profile-header c-blk br-grey">CLIENTELE</h5>
                                <div class="developer-work-agencies pd-t30">
                                    <div class="agencies-wrap">
                                        <div class="agency">
                                            <span class="counter">{{ $sectionData['clientele']->model->agencies }}</span>
                                            <h6>Agencies</h6>
                                        </div>
                                        <div class="agency">
                                            <span class="counter">{{ $sectionData['clientele']->model->companies }}</span>
                                            <h6>Companies</h6>
                                        </div>
                                    </div>
                                    <div class="fleg-wrap">
                                        <ul>
                                            @foreach ($sectionData['clientele']->model->countries as $item)
                                            <li>
                                                <figure><img src="{{ asset('img/country/'.$item.'.svg') }}" width="54px" height="32px"></figure>
                                                <h6>{{ strtoupper($item) }}</h6>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                                <div class="developer-work-rating">
                                    <div class="work-box">
                                        <div class="work-box-wrap">
                                            <span><span class="counter">{{ $sectionData['clientele']->model->metric->count }}</span>+</span>
                                            <h6>{{ $sectionData['clientele']->model->metric->title }}</h6>
                                        </div>
                                    </div>
                                    <div class="work-box">
                                        <div class="work-box-wrap">
                                            <div>
                                                <span class="rating-star-container">
                                                    <span class="active-stars" style="width: {{ $sectionData['clientele']->model->overallRating * 30 }}px"></span>
                                                    <span class="inactive-stars"></span>
                                                </span>
                                            </div>
                                            <h6>Overall Project Rating</h6>
                                        </div>
                                    </div>
                                    <div class="work-box">
                                        <div class="work-box-wrap">
                                            <div>
                                                <span class="rating-star-container">
                                                    <span class="active-stars" style="width: {{ $sectionData['clientele']->model->clientSatisfaction * 30 }}px"></span>
                                                    <span class="inactive-stars"></span>
                                                </span>
                                            </div>
                                            <h6>Client Satisfaction</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    @endif
                    @if (isset($sectionData['plus_points']) && $sectionData['plus_points']->display)
                    <template x-if="listingItem == 'plus_points'">
                        <div class="section-2 section pdf-section animated-highlight" @auth :class="{'drag': isInEditMode}" @endauth id="plus_points">
                            <div class="main">
                                <div class="pluspoint-black-box">
                                    <div class="profile-header c-white">{{ strtoupper($sectionData['plus_points']->label) }}</div>
                                    <div class="profile-desc">{{ $sectionData['plus_points']->model }}</div>
                                </div>
                            </div>
                        </div>
                    </template>
                    @endif
                    @if ($esps->count() > 0 && $sectionData['esp']->display)
                    <template x-if="listingItem == 'esp'">
                        <div class="section-5 section pdf-section animated-highlight" @auth :class="{'drag': isInEditMode}" @endauth id="esp">
                            <div class="main">
                                <div class="profile-header c-blk br-grey">{{ strtoupper($sectionData['esp']->label) }}</div>
                                <div class="flex-container pd-t30">
                                    @foreach ($esps as $management)
                                    <div class="block"><img src="{{ asset('img/esp/'.$management->icon) }}" /></div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </template>
                    @endif
                    @if ($pms->count() > 0 && $sectionData['pms']->display)
                    <template x-if="listingItem == 'pms'">
                        <div class="section-5 section pdf-section animated-highlight" @auth :class="{'drag': isInEditMode}" @endauth id="pms">
                            <div class="main">
                                <div class="profile-header c-blk br-grey">{{ strtoupper($sectionData['pms']->label) }}</div>
                                <div class="flex-container pd-t30">
                                    @foreach ($pms as $management)
                                    <div class="block"><img src="{{ asset('img/pms/'.$management->icon) }}" /></div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </template>
                    @endif
                    @if ($frameworks->count() > 0 && $sectionData['frameworks']->display)
                    <template x-if="listingItem == 'frameworks'">
                        <div class="section-5 section pdf-section animated-highlight" @auth :class="{'drag': isInEditMode}" @endauth id="frameworks">
                            <div class="main">
                                <div class="profile-header c-blk br-grey">{{ strtoupper($sectionData['frameworks']->label) }}</div>
                                <div class="flex-container pd-t30">
                                    @foreach ($frameworks as $management)
                                    <div class="block"><img src="{{ asset('img/framework/'.$management->icon) }}" /></div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </template>
                    @endif
                    @if(isset($sectionData['hobbies']) && $sectionData['hobbies']->display && sizeof($sectionData['hobbies']->model) > 0)
                    <template x-if="listingItem == 'hobbies'">
                        <div class="section-6 section pdf-section animated-highlight" @auth :class="{'drag': isInEditMode}" @endauth id="hobbies">
                            <div class="main">
                                <div class="profile-header c-blk br-grey">INTERESTS</div>
                                <div class="flex-container pd-t30">
                                    @foreach ($sectionData['hobbies']->model as $hobby)
                                    <div class="block pad-right">
                                        <div class="hobby-circle"><span>{{ $hobby->title }}</span></div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </template>
                    @endif
                    <!-- custom sections -->

                    @foreach ($sectionData as $key=>$sect)
                    @isset($sect->type)
                    @if ($sect->type == 'custom' && $sect->display)
                    <template x-if="listingItem == '{{ $key }}'">
                        <div class="section-1 section pdf-section animated-highlight" @auth :class="{'drag': isInEditMode}" @endauth id="{{ $key }}">
                            <div class="main">
                                <div class="black-box" style="background-color: {{ $sect->attrs->bg }} !important;">
                                    <div class="profile-header" style="color: {{ $sect->attrs->text }} !important">{{ strtoupper($sect->label) }}</div>
                                    <div class="profile-desc" style="color: {{ $sect->attrs->text }} !important">
                                        @if ($sect->customType == 'text')
                                        {{ $sect->model }}
                                        @elseif($sect->customType == 'bullet-text')
                                        <ul>
                                            @foreach ($sect->model as $model)
                                            <li style="color: {{ $sect->attrs->text }} !important">
                                                {{ $model->model }}
                                            </li>
                                            @endforeach
                                        </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    @endif
                    @endisset
                    @endforeach
                    <!-- /custom sections -->
                </div>
            </template>
        </div>
        <!--mid container ends here-->

        <!--footer starts here-->
        <footer class="flw"></footer>
        <!--footer ends here-->
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.js"></script>
    <script type="text/javascript" src="{{ asset('frontend/template1/js/jquery.easypiechart.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.13.0/Sortable.min.js" integrity="sha512-5x7t0fTAVo9dpfbp3WtE2N6bfipUwk7siViWncdDoSz2KwOqVC1N9fDxEOzk0vTThOua/mglfF8NO7uVDLRC8Q==" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.0/dist/alpine.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(window).load(function(){
            let hashParam = "{{ $highlightSection }}";
            if(hashParam != 'n_a'){
                let element = $("#"+hashParam);
                if(element.length > 0){                
                    let elementOffset = element.offset().top;
                    $('html, body').animate({
                        scrollTop: elementOffset
                    }, 1000,() => {
                        element.addClass('pulsate')
                        setTimeout(() => {
                            element.removeClass('pulsate')
                        }, 5000);
                    });
                }
                
            }

            @if (!$hiddenPdf && Auth::check())    
                // get page height
                let pageHeight = $(".wrapper").height();
                let downloadButton = $("#download-pdf");
                let downloadButtonHref = downloadButton.attr('href');
                downloadButton.attr('href', downloadButtonHref.replace('::JSCALCULATEDHEIGHT::', pageHeight));
            @endif

        })
        $(document).ready(function(){  
            $(".chart").easyPieChart({
                scaleColor: "#fff",
                lineWidth: 2,
                lineCap: "butt",
                barColor: "#ffda30",
                trackColor: "#EEE",
                size: 200,
                animate: 2000,
            });
        });

        @if (!$hiddenLayout && Auth::check())
            // Sprucejs and alpinejs code
            let layoutCopy = []
            let isInEditMode = false;
        @endif
        
        function layoutManager(){
            return {
                isInEditMode: false,
                layout: [
                    "gallery",
                    "profile",
                    "experience",
                    "education",
                    "qualities",
                    "achievements",
                    "clientele",
                    "plus_points",
                    "testimonial",
                    "fact",
                    "esp",
                    "pms",
                    "frameworks",
                    "capabilities",
                    "hobbies",
                ],
                customSections : [],
                initLayoutManager: function(){
                    let sectionsValueArray = [];
                    let sectionsJson = @if(isset($sectionData)) @json($sectionData) @else '' @endif;
                    if(sectionsJson != ""){
                        for (const key in sectionsJson) {
                            if (Object.hasOwnProperty.call(sectionsJson, key)) {
                                const element = sectionsJson[key];
                                sectionsValueArray.push(key)
                                
                            }
                        }
                    }

                    var incomingLayoutJson = @if(isset($portfolio->layout))  @json($portfolio->layout) @else '' @endif;
                    if(incomingLayoutJson != ''){
                        var parsedIncomingJson = JSON.parse(incomingLayoutJson);
                        var merger = parsedIncomingJson.concat(sectionsValueArray.filter((item) => parsedIncomingJson.indexOf(item) < 0))
                        this.layout = merger;
                    } else {
                        var merger = this.layout.concat(sectionsValueArray.filter((item) => this.layout.indexOf(item) < 0))
                        this.layout = merger;
                    }
                },
                @if (!$hiddenLayout && Auth::check())
                enableDragAndDrop(){
                    this.isInEditMode = true;
                    isInEditMode = true;
                    layoutCopy = [...this.layout];
                },
                saveChanges(){
                    var isSendingResponse = false;
                    var stringified = JSON.stringify(layoutCopy)
                    if(!isSendingResponse){
                        $.ajax({
                            type: 'POST',
                            url: "{{ route('public.portfolio.layout-update', urlencode($portfolioSlug)) }}",
                            data: { "_token": "{{ csrf_token() }}", layout: stringified },
                            success: (data) => {
                                isSendingResponse = false;

                                if(data.success){

                                    this.isInEditMode = false;
                                    isInEditMode = false;
                                    this.layout = [...layoutCopy];


                                    this.$nextTick(() => {
                                        $(".chart").easyPieChart({
                                            scaleColor: "#fff",
                                            lineWidth: 2,
                                            lineCap: "butt",
                                            barColor: "#ffda30",
                                            trackColor: "#EEE",
                                            size: 200,
                                            animate: 2000,
                                        });
                                    });
                                    toastr.success('Successfuly saved layout')
                                }   else{
                                    toastr.error('Error when saving layout')
                                    this.isInEditMode = false;
                                }
                            }
                        });
                    }
                },
                disableDragAndDrop(){
                    this.isInEditMode = false;
                    isInEditMode = false;
                },
                @endif
            }
        }
        @if (!$hiddenLayout && Auth::check())
            new Sortable(document.querySelectorAll(".mid-container")[0], {
                animation: 150,
                handle: ".drag",
                onEnd: function (evt) {
                    let oldIndex = evt.oldIndex;
                    let newIndex = evt.newIndex;
                    if(isInEditMode){
                        let item = layoutCopy.splice(oldIndex,1);
                        layoutCopy.splice(newIndex,0, ...item);
                    }
                },
            });
        @endif
    </script>
</body>

</html>