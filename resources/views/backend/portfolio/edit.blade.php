@extends('core.backend.app', ['pageTitle' => 'Portfolios'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/plugins/bs-stepper/bs-stepper.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/plugins/dropzone/basic.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/device-preview.css') }}">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bs-stepper/dist/js/bs-stepper.min.js"></script>
    <script src="{{ asset('backend/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/spruce/spruce.umd.js') }}"></script>
    <script src="{{ asset('backend/plugins/alpinejs/alpine.min.js') }}"></script>
    <script src="{{ asset('backend/js/deep-clone.js') }}"></script>
    <script src="{{ asset('backend/js/validity.min.js') }}"></script>
    <script src="{{ asset('backend/js/hyperform.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/dropzone/dropzone.min.js') }}"></script>
    <script>
        Dropzone.options.gallery = {
            url: "{{ route('admin.portfolio.edit.gallery.upload') }}",
            previewTemplate: $("#preview-template").html(),
            dictDefaultMessage: "Drop files here or click to select",
            addRemoveLinks: true,
            uploadMultiple: true,
            maxFilesize: 2,
            filesizeBase: 1024,
            acceptedFiles: "image/jpg,image/jpeg,image/png",
            init: function() {
                this.renderExistingServerFiles = function(files, fileUrls, response) {
                    for (const file in files) {
                        if (Object.hasOwnProperty.call(files, file)) {
                            const element = files[file];

                            this.files.push(element);
                            this.displayExistingFile(element, fileUrls[file], null, null, true);
                            this.emit("processing", element);
                            this.emit("complete", element);
                        }
                    }
                    this.emit("successmultiple", files, response, false);
                }

                @isset($portfolioCopy->galleryImages)
                    let files = [];
                    let fileUrls = [];
                    let response = {status:"success", fileHashes: []};
                    @foreach ($portfolioCopy->galleryImages as $image)
                        @php
                            $imageUrl = asset('img/portfolio-gallery/' . $image->image_hash);
                            $imagePath = public_path('img/portfolio-gallery/' . $image->image_hash);
                            $fileExists = File::exists($imagePath);
                            if ($fileExists) {
                                $imageMime = File::mimeType($imagePath);
                                $imageSize = File::size($imagePath);
                                $imageName = File::name($imagePath);
                            }
                        @endphp
                    
                        @if ($fileExists)
                            files.push({
                            processing: true,
                            accepted: true,
                            name: "{{ $imageName }}",
                            size: '{{ $imageSize }}',
                            type: '{{ $imageMime }}',
                            status: Dropzone.SUCCESS,
                            });
                    
                            fileUrls.push("{{ $imageUrl }}")
                            response.fileHashes.push("{{ $image->image_hash }}");
                        @endif
                    @endforeach
                    this.renderExistingServerFiles(files, fileUrls, response);
                @endisset
            },
            sending: function(file, xhr, formData) {
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('portfolioId', '{{ $portfolioCopy->id }}');
            },
            successmultiple: function(files, response) {
                let store = Spruce.store('previewRenderJson').details;


                let imgHashes = [];
                if (files.length > 0) {


                    for (const [index, file] of files.entries()) {
                        let imageHash = response.fileHashes[index];
                        let previewElement = $(file.previewElement);
                        previewElement.attr('data-file-hash', imageHash);

                        imgHashes.push(imageHash);
                        previewElement.append(`<input type="text" name="gallery_image_hashes[]" value="${imageHash}" hidden/>`)
                    }
                }

                store.gallery_image_hashes = [...store.gallery_image_hashes, ...imgHashes];
                buildPreviews().validatePreviewConditions()
            },
            removedfile: function(file) {
                let store = Spruce.store('previewRenderJson').details;
                let previewElement = $(file.previewElement);
                let imageHash = previewElement.data("file-hash");

                let imageHashClone = deepClone(store.gallery_image_hashes);
                let toDelete = imageHashClone.filter((el) => {
                    return el == imageHash
                });
                store.gallery_image_hashes = imageHashClone.filter((el) => {
                    return el != imageHash
                });
                store.to_erase_gallery_image_hashes = [...store.to_erase_gallery_image_hashes, ...toDelete];
                previewElement.remove();
            }
        }

        Dropzone.options.profile = {
            url: "{{ route('admin.portfolio.edit.profile-photo.upload') }}",
            previewTemplate: $("#preview-template").html(),
            dictDefaultMessage: "Drop photo here or click to select <br> (Image must be square)",
            addRemoveLinks: true,
            uploadMultiple: false,
            maxFilesize: 2,
            maxFiles: 1,
            maxfilesexceeded: function(file) {
                this.removeFile(file);
            },
            filesizeBase: 1024,
            acceptedFiles: "image/jpg,image/jpeg,image/png",
            init: function() {
                this.on("thumbnail", function(file) {
                    if (file.width != file.height) {
                        this.removeFile(file)
                        toastr.error("Profile image must be a square");
                    }
                });
                this.renderExistingProfile = function(file, fileUrl, response) {
                    this.files.push(file);
                    this.displayExistingFile(file, fileUrl, null, null, true);
                    this.emit("processing", file);
                    this.emit("complete", file);

                    this.emit("success", file, response, false);
                }

                @isset($portfolioCopy->profile_photo)
                    let response = {status:"success", fileHash: ""};
                    @php
                    $imageUrl = asset('img/portfolio-profile/' . $portfolioCopy->profile_photo);
                    $imagePath = public_path('img/portfolio-profile/' . $portfolioCopy->profile_photo);
                    $fileExists = File::exists($imagePath);
                    if ($fileExists) {
                        $imageMime = File::mimeType($imagePath);
                        $imageSize = File::size($imagePath);
                        $imageName = File::name($imagePath);
                    }
                    @endphp
                
                    @if ($fileExists)
                        this.renderExistingProfile(
                        {
                        processing: true,
                        accepted: true,
                        name: "{{ $imageName }}",
                        size: {{ $imageSize }},
                        type: '{{ $imageMime }}',
                        status: Dropzone.SUCCESS,
                        },
                        "{{ $imageUrl }}",
                        {
                        status:"success",
                        fileHash: "{{ $portfolioCopy->profile_photo }}"
                        }
                        );
                    @endif
                @endisset
            },
            sending: function(file, xhr, formData) {
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('portfolioId', '{{ $portfolioCopy->id }}');
            },
            success: function(file, response) {
                let store = Spruce.store('previewRenderJson').details;

                let imageHash = response.fileHash;
                let previewElement = $(file.previewElement);
                previewElement.attr('data-file-hash', imageHash);

                previewElement.append(`<input type="text" name="profile_image_hash" value="${imageHash}" hidden/>`)


                store.profile_image_hash = imageHash;
                buildPreviews().validatePreviewConditions()
            },
            removedfile: function(file) {
                let store = Spruce.store('previewRenderJson').details;
                let previewElement = $(file.previewElement);
                let imageHash = previewElement.data("file-hash");

                let imageHashClone = deepClone(store.profile_image_hash);
                if (imageHashClone == imageHash) {
                    store.profile_image_hash = ''
                }
                previewElement.remove();
            }
        }

        var stepper;
        stepper = new Stepper(document.getElementsByClassName('bs-stepper')[0], {
            linear: false,
            animation: true,
            selectors: {
                steps: '.step',
                trigger: '.step-trigger',
                stepper: '.bs-stepper'
            }
        })
        $(document).ready(function() {
            let form = document.getElementById("portfolio-form");
            $(document).on('change', ".form-control", function(e) {
                let value = $(this).val();
                if (value != null && value != "") {
                    $(this).removeClass('is-invalid')
                }
            })

            let hyperForm = hyperform(window);

            $(".select2").select2({
                theme: 'bootstrap4'
            });

            $('.highlight-section').on("click", function() {
                let id = $(this).attr('data-highlight-id');
                let iframe = $('#preview-iframe');
                let iframeSrc = "{{ route('admin.portfolio.portfolio-preview.show', ['previewId' => $portfolioCopy->temp_version_id, 'highlightSection' => '::param::']) }}".replace('::param::', id);
                iframe.attr('src', iframeSrc)
            })

            $(document).on('submit', "#portfolio-form", function(e) {
                e.preventDefault()
                var is_valid = document.forms[0].reportValidity();
                if (is_valid) {
                    form.submit()
                } else {
                    let getAllFailed = []
                    for (const element of form.getElementsByClassName('form-control')) {
                        $(element).removeClass('is-invalid')
                        if (!element.validity.valid) {
                            getAllFailed.push(element)
                        }
                    }

                    if (getAllFailed.length > 0) {
                        let firstFailed = getAllFailed[0];
                        $(firstFailed).addClass('is-invalid')
                        let stepNo = $(firstFailed).closest(".stepper-content").attr('data-step-no');
                        stepper.to(stepNo)
                    }

                    toastr.error('One or more fields are invalid');
                }
            });


            $('.highlight-section').popover({
                content: "Click to view in theme",
                trigger: "hover"
            })
        })

        $(document).on("keypress", "#experience-years-years, #clientele-agency, #clientele-companies", function(event) {
            evt = (event) ? event : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 32 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        });

    </script>
    @include('backend.portfolio.edit-scripts')
@endpush

@section('page-content')
    <div x-data="{
                                            ...sectionData(), 
                                            ...sectionDataManipulate(), 
                                            ...buildPreviews(),
                                            ...checkSlugValue()
                                        }" x-init="() => { 
                                            sectionTypesInit(); 
                                            onAppInit(); 
                                        }">
        <div class="container-fluid">
            <div class="bs-stepper w-100">
                <div class="flex-wrap d-flex bs-stepper-header w-100 " role="tablist">
                    <!-- your steps here -->
                    <div class="step flex-grow-1 d-flex" data-target="#general-step">
                        <button type="button" class="step-trigger flex-column" role="tab">
                            <span class="bs-stepper-circle">1</span>
                            <span class="bs-stepper-label">General</span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step flex-grow-1 d-flex" data-target="#profile-step">
                        <button type="button" class="step-trigger flex-column" role="tab">
                            <span class="bs-stepper-circle">2</span>
                            <span class="bs-stepper-label">Profile</span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step flex-grow-1 d-flex" data-target="#experience-step">
                        <button type="button" class="step-trigger flex-column" role="tab">
                            <span class="bs-stepper-circle">3</span>
                            <span class="bs-stepper-label">Experience</span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step flex-grow-1 d-flex" data-target="#education-step">
                        <button type="button" class="step-trigger flex-column" role="tab">
                            <span class="bs-stepper-circle">4</span>
                            <span class="bs-stepper-label">Education</span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step flex-grow-1 d-flex" data-target="#achievements-qualities-step">
                        <button type="button" class="step-trigger flex-column" role="tab">
                            <span class="bs-stepper-circle">5</span>
                            <span class="bs-stepper-label">Achievement + Qualities</span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step flex-grow-1 d-flex" data-target="#clientele-testimonial-step">
                        <button type="button" class="step-trigger flex-column" role="tab">
                            <span class="bs-stepper-circle">6</span>
                            <span class="bs-stepper-label">Clientele + Testimonial</span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step flex-grow-1 d-flex" data-target="#plus-point-fun-fact-step">
                        <button type="button" class="step-trigger flex-column" role="tab">
                            <span class="bs-stepper-circle">7</span>
                            <span class="bs-stepper-label">Plus Points + Fun Fact</span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step flex-grow-1 d-flex" data-target="#masters-step">
                        <button type="button" class="step-trigger flex-column" role="tab">
                            <span class="bs-stepper-circle">8</span>
                            <span class="bs-stepper-label">Masters</span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step flex-grow-1 d-flex" data-target="#gallery-step">
                        <button type="button" class="step-trigger flex-column" role="tab">
                            <span class="bs-stepper-circle">9</span>
                            <span class="bs-stepper-label">Gallery</span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step flex-grow-1 d-flex" data-target="#hobby-step">
                        <button type="button" class="step-trigger flex-column" role="tab">
                            <span class="bs-stepper-circle">10</span>
                            <span class="bs-stepper-label">Hobbies</span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step flex-grow-1 d-flex" data-target="#custom-step">
                        <button type="button" class="step-trigger flex-column" role="tab">
                            <span class="bs-stepper-circle">11</span>
                            <span class="bs-stepper-label">Custom</span>
                        </button>
                    </div>
                </div>
                <div class="mt-2 bs-stepper-content">
                    <form method="POST" id="portfolio-form" action="{{ route('admin.portfolio.update', $portfolioCopy->portfolio_id) }}">
                        @csrf
                        @method('PATCH')
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="card">
                                    <input type="text" name="section_data" class="form-control" x-bind:value="(localSectionsJson.length > 0) ? JSON.stringify(localSectionsJson) : ''" hidden required>
                                    <div id="general-step" class="content stepper-content" role="tabpanel" data-step-no="1">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                General
                                            </h3>
                                        </div>
                                        <!-- /.card-header -->
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="first-name">First Name</label>
                                                <input type="text" name="first-name" class="form-control @error('first-name') is-invalid @enderror" id="first-name" placeholder="Enter first name" value="{{ old('first-name', $portfolioCopy->first_name) }}" @input="onValueEntry('first-name', $event.target.value)" autofocus required>
                                                @error('first-name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="last-name">Last Name</label>
                                                <input type="text" name="last-name" class="form-control @error('last-name') is-invalid @enderror" id="last-name" placeholder="Enter last name" value="{{ old('last-name', $portfolioCopy->last_name) }}" @keyup="onValueEntry('last-name', $event.target.value)" required>
                                                @error('last-name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Email address</label>
                                                <input type="email" name="email" pattern="^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Enter email" value="{{ old('email', $portfolioCopy->email) }}" @input="onValueEntry('email', $event.target.value)" required>
                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="mobile">Mobile</label>
                                                <input type="tel" name="mobile" class="form-control @error('mobile') is-invalid @enderror" id="mobile" placeholder="Enter mobile" value="{{ old('mobile', $portfolioCopy->mobile) }}" @input="onValueEntry('mobile', $event.target.value)" maxlength="13" minlength="10" required>
                                                @error('mobile')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="template">Template</label>
                                                <div class="row">
                                                    <div :class="($store.previewRenderJson.details.template != '' &&  $store.previewRenderJson.details.template != null)? 'col-10' : 'col-12'">
                                                        <select name="template" class="form-control @error('template') is-invalid @enderror" id="template" @change="onValueEntry('template', $event.target.value)" required>
                                                            @foreach ($availableTemplates as $key => $item)
                                                                <option value="{{ $item }}" {{ $item == old('template', $portfolioCopy->template_id) ? 'selected' : '' }}>{{ $key }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <template x-if="($store.previewRenderJson.details.template != '' &&  $store.previewRenderJson.details.template != null)">
                                                        <div class="col-2">
                                                            <a x-bind:href="`{{ route('public.portfolio', ['portfolioSlug' => 'dummy', 'templateId' => '::templateId::']) }}`.replace('::templateId::', $store.previewRenderJson.details.template)" target="_blank" class="btn btn-info btn-block" title="preview"><i class="fas fa-eye"></i></a>
                                                        </div>
                                                    </template>
                                                </div>

                                                @error('template')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="designation">Designation</label>
                                                <input type="text" name="designation" class="form-control @error('designation') is-invalid @enderror" id="designation" placeholder="Enter designation" value="{{ old('designation', isset($portfolioCopy->designation) ? $portfolioCopy->designation : 'Your Designation') }}" @keyup="onValueEntry('designation', $event.target.value)" required>
                                                @error('designation')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="skill_level">Skill Level</label>
                                                <input type="text" name="skill_level" class="form-control @error('skill_level') is-invalid @enderror" id="skill_level" placeholder="Enter skill level" value="{{ old('skill_level', isset($portfolioCopy->skill_level) ? $portfolioCopy->skill_level : 'Your Skill Level') }}" @keyup="onValueEntry('skill_level', $event.target.value)" required>
                                                @error('skill_level')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="slug">Url Slug</label>
                                                <div class="d-flex">
                                                    <p class="py-2 mb-0 mr-2 text-secondary">{{ env('APP_URL') }}/portfolio/</p>
                                                    <div>
                                                        <input type="text" name="slug" class="w-auto d-inline form-control" :class="(!slugInputValid)? 'is-invalid': ''" id="slug" placeholder="firstname-lastname-123" x-on:input.debounce.750="onSlugInput($event)" value="{{ old('slug', isset($portfolioCopy->slug) ? $portfolioCopy->slug : '') }}" @keyup="onValueEntry('slug', $event.target.value)" required>
                                                        <template x-if="!slugInputValid">
                                                            <span class="invalid-feedback d-block" role="alert">
                                                                <strong x-text="slugInputValidFeedback"></strong>
                                                            </span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" name="active" class="custom-control-input" id="account_active" @change="onValueEntry('active', $event.target.value)" {{ $portfolioCopy->active ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="account_active">Activate profile?</label>
                                                </div>
                                                @error('active')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="button" onclick="stepper.previous()" class="btn btn-primary">Previous</button>
                                            <button type="button" onclick="stepper.next()" class="btn btn-primary">Next</button>
                                        </div>
                                        <!-- /.card-footer -->
                                    </div>
                                    <div id="profile-step" class="content stepper-content" role="tabpanel" data-step-no="2">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                Profile
                                            </h3>
                                            <div class="card-tools">
                                                <div class="btn-group">
                                                    <div class="card-tools">
                                                        <button type="button" class="btn btn-tool highlight-section" data-highlight-id="profile"><i class="fas fa-question-circle"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.card-header -->
                                        <div class="card-body">
                                            <div>
                                                <div class="form-group">
                                                    <label for="profile-label">Section Title</label>
                                                    <input type="text" class="form-control" id="profile-label" x-bind:value="(Object.keys(getObjectByValue('profile', localSectionsJson)).length != 0)? getObjectByValue('profile', localSectionsJson).label: ''" @input="updateLabels('profile', $event.target.value)" placeholder="Enter label" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="profile">Content</label>
                                                    <textarea class="form-control" x-bind:value="(Object.keys(getObjectByValue('profile', localSectionsJson)).length != 0)? getObjectByValue('profile', localSectionsJson).model: ''" @input="updateSimpleModel('profile', $event.target.value)" placeholder="Enter profile content" required></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="profile">Profile Image</label>
                                                    <div class="dropzone" id="profile"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="button" onclick="stepper.previous()" class="btn btn-primary">Previous</button>
                                            <button type="button" onclick="stepper.next()" class="btn btn-primary">Next</button>
                                        </div>
                                        <!-- /.card-footer -->
                                    </div>
                                    <div id="experience-step" class="content stepper-content" role="tabpanel" data-step-no="3">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                Experience
                                            </h3>
                                            <div class="card-tools">
                                                <div class="btn-group">
                                                    <div class="card-tools d-flex">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input" id="experience-display" value="1" @change="updateDisplayToggle('experience',$event.target.checked)" x-bind:checked="(Object.keys(getObjectByValue('experience', localSectionsJson)).length > 0)? getObjectByValue('experience', localSectionsJson).display: true">
                                                            <label class="custom-control-label" for="experience-display">Display?</label>
                                                        </div>
                                                        <button type="button" class="btn btn-tool highlight-section" data-highlight-id="experience"><i class="fas fa-question-circle"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.card-header -->
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="experience-label">Section Title</label>
                                                <input type="text" class="form-control" id="experience-label" x-bind:value="(Object.keys(getObjectByValue('experience', localSectionsJson)).length > 0)? getObjectByValue('experience', localSectionsJson).label: ''" @input="updateLabels('experience', $event.target.value)" placeholder="Enter label" required>
                                            </div>
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    Experiences
                                                </h3>
                                                <div class="card-tools">
                                                    <div class="btn-group">
                                                        <div class="card-tools">
                                                            <button type="button" class="btn btn-tool" @click="addExperience()"><i class="fas fa-plus"></i> Add</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <template x-if="Object.keys(getObjectByValue('experience', localSectionsJson)).length > 0">
                                                    <div>
                                                        <template x-for="(experience, experienceIndex) in getObjectByValue('experience', localSectionsJson).model" :key="experienceIndex">
                                                            <div class="card card-info">
                                                                <div class="card-header">
                                                                    <h3 class="card-title">Experience <span x-text="experienceIndex+1"></span></h3>
                                                                    <template x-if="getObjectByValue('experience', localSectionsJson).model.length > 1">
                                                                        <div class="card-tools">
                                                                            <button type="button" class="btn btn-tool" @click="removeExperience(experienceIndex)">
                                                                                <i class="fas fa-times"></i>
                                                                            </button>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="form-group">
                                                                        <label x-bind:for="`experience-job-title-${experienceIndex}`">Job Title</label>
                                                                        <input type="text" class="form-control" x-bind:id="`experience-job-title-${experienceIndex}`" x-bind:value="experience.title" @input="updateExperience('experience', experienceIndex, 'title', $event.target.value)" placeholder="Enter job title" required>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label x-bind:for="`experience-company-${experienceIndex}`">Company Name</label>
                                                                        <input type="text" class="form-control" x-bind:id="`experience-company-${experienceIndex}`" x-bind:value="experience.company" @input="updateExperience('experience', experienceIndex, 'company', $event.target.value)" placeholder="Enter company name" required>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="form-group col-12 col-md-6">
                                                                            <label x-bind:for="`experience-start-${experienceIndex}`">Start Date (eg. Jan 2001)</label>
                                                                            <input type="text" class="form-control" x-bind:id="`experience-start-${experienceIndex}`" x-bind:value="experience.start" @input="updateExperience('experience', experienceIndex, 'start', $event.target.value)" placeholder="Enter start date" required>
                                                                        </div>
                                                                        <div class="form-group col-12 col-md-6">
                                                                            <label x-bind:for="`experience-end-${experienceIndex}`">End Date (eg. Jan 2020)</label>
                                                                            <input type="text" class="form-control" x-bind:id="`experience-end-${experienceIndex}`" x-bind:value="experience.end" @input="updateExperience('experience', experienceIndex, 'end', $event.target.value)" placeholder="Enter end date" required>
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <div class="align-items-center d-flex justify-content-between">
                                                                            <h3 class="card-title">Experience Content</h3>
                                                                            <div class="card-tools">
                                                                                <div class="btn-group">
                                                                                    <div class="card-tools">
                                                                                        <button type="button" class="btn btn-tool text-black-50" @click="addExperienceContent(experienceIndex)"><i class="fas fa-plus"></i> Add</button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mt-5">
                                                                            <template x-for="(experienceContent, experienceContentIndex) in experience.content" :key="experienceContentIndex">
                                                                                <div class="form-group">
                                                                                    <label x-bind:for="`experience-years-content-${experienceIndex}-${experienceContentIndex}`" class="d-flex align-items-center justify-content-between">
                                                                                        <span>Content</span>
                                                                                        <template x-if="experience.content.length > 1">
                                                                                            <div class="card-tools">
                                                                                                <button type="button" class="btn btn-tool text-black-50" @click="removeExperienceContent(experienceIndex, experienceContentIndex)">
                                                                                                    <i class="fas fa-times"></i>
                                                                                                </button>
                                                                                            </div>
                                                                                        </template>
                                                                                    </label>
                                                                                    <textarea class="form-control" x-bind:id="`experience-years-content-${experienceIndex}-${experienceContentIndex}`" x-bind:value="experienceContent.model" @input="updateExperienceContent('experience', experienceIndex, experienceContentIndex, $event.target.value)" placeholder="Enter experience content" required></textarea>
                                                                                </div>
                                                                            </template>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="button" onclick="stepper.previous()" class="btn btn-primary">Previous</button>
                                            <button type="button" onclick="stepper.next()" class="btn btn-primary">Next</button>
                                        </div>
                                        <!-- /.card-footer -->
                                    </div>
                                    <div id="education-step" class="content stepper-content" role="tabpanel" data-step-no="4">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                Education
                                            </h3>
                                            <div class="card-tools">
                                                <div class="btn-group">
                                                    <div class="card-tools">
                                                        <button type="button" class="btn btn-tool highlight-section" data-highlight-id="education"><i class="fas fa-question-circle"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.card-header -->
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="education-label">Section Title</label>
                                                <input type="text" class="form-control" id="education-label" x-bind:value="(Object.keys(getObjectByValue('education', localSectionsJson)).length > 0 ) ? getObjectByValue('education', localSectionsJson).label: ''" @input="updateLabels('education', $event.target.value)" placeholder="Enter label" required>
                                            </div>
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    Education History
                                                </h3>
                                                <div class="card-tools">
                                                    <div class="btn-group">
                                                        <div class="card-tools">
                                                            <button type="button" class="btn btn-tool" @click="addEducation()"><i class="fas fa-plus"></i> Add</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <template x-if="Object.keys(getObjectByValue('education', localSectionsJson)).length > 0">
                                                    <div>
                                                        <template x-for="(education, experienceIndex) in getObjectByValue('education', localSectionsJson).model" :key="experienceIndex">
                                                            <div class="card card-info">
                                                                <div class="card-header">
                                                                    <h3 class="card-title">Education <span x-text="experienceIndex+1"></span></h3>
                                                                    <template x-if="getObjectByValue('education', localSectionsJson).model.length > 1">
                                                                        <div class="card-tools">
                                                                            <button type="button" class="btn btn-tool" @click="removeEducation(experienceIndex)">
                                                                                <i class="fas fa-times"></i>
                                                                            </button>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="form-group">
                                                                        <label x-bind:for="`education-course-${experienceIndex}`">Course name</label>
                                                                        <input type="text" class="form-control" x-bind:id="`education-course-${experienceIndex}`" x-bind:value="education.course" @input="updateEducation('education', experienceIndex, 'course', $event.target.value)" placeholder="Enter course name" required>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label x-bind:for="`education-institution-${experienceIndex}`">Institution Name</label>
                                                                        <input type="text" class="form-control" x-bind:id="`education-institution-${experienceIndex}`" x-bind:value="education.institution" @input="updateEducation('education', experienceIndex, 'institution', $event.target.value)" placeholder="Enter institution name" required>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="form-group col-12 col-md-6">
                                                                            <label x-bind:for="`education-start-${experienceIndex}`">Start Date (eg. Jan 2001)</label>
                                                                            <input type="text" class="form-control" x-bind:id="`education-start-${experienceIndex}`" x-bind:value="education.start" @input="updateEducation('education', experienceIndex, 'start', $event.target.value)" placeholder="Enter start date" required>
                                                                        </div>
                                                                        <div class="form-group col-12 col-md-6">
                                                                            <label x-bind:for="`education-end-${experienceIndex}`">End Date (eg. Jan 2020)</label>
                                                                            <input type="text" class="form-control" x-bind:id="`education-end-${experienceIndex}`" x-bind:value="education.end" @input="updateEducation('education', experienceIndex, 'end', $event.target.value)" placeholder="Enter end date" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label x-bind:for="`education-years-content-${experienceIndex}`">Education Content</label>
                                                                        <textarea class="form-control" x-bind:id="`education-years-content-${experienceIndex}`" x-bind:value="education.content" @input="updateEducation('education', experienceIndex, 'content', $event.target.value)" placeholder="Enter education content"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="button" onclick="stepper.previous()" class="btn btn-primary">Previous</button>
                                            <button type="button" onclick="stepper.next()" class="btn btn-primary">Next</button>
                                        </div>
                                        <!-- /.card-footer -->
                                    </div>
                                    <div id="achievements-qualities-step" class="content stepper-content" role="tabpanel" data-step-no="5">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                Achievements + Qualities
                                            </h3>
                                        </div>
                                        <!-- /.card-header -->
                                        <div class="card-body">
                                            <div class="card-header">
                                                <h3 class="card-title">Achievements</h3>
                                                <div class="card-tools">
                                                    <div class="btn-group">
                                                        <div class="card-tools d-flex">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="achievement-display" value="1" @change="updateDisplayToggle('achievements',$event.target.checked)" x-bind:checked="(Object.keys(getObjectByValue('achievements', localSectionsJson)).length > 0)? getObjectByValue('achievements', localSectionsJson).display: true">
                                                                <label class="custom-control-label" for="achievement-display">Display?</label>
                                                            </div>
                                                            <button type="button" class="btn btn-tool highlight-section" data-highlight-id="achievements"><i class="fas fa-question-circle"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="achievements-label">Section Title</label>
                                                    <input type="text" class="form-control" id="achievements-label" x-bind:value="(Object.keys(getObjectByValue('achievements', localSectionsJson)).length > 0)? getObjectByValue('achievements', localSectionsJson).label: ''" @input="updateLabels('achievements', $event.target.value)" placeholder="Enter label" required>
                                                </div>
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        Achievement History
                                                    </h3>
                                                    <div class="card-tools">
                                                        <div class="btn-group">
                                                            <div class="card-tools">
                                                                <button type="button" class="btn btn-tool" @click="addAchievement()"><i class="fas fa-plus"></i> Add</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <template x-if="Object.keys(getObjectByValue('achievements', localSectionsJson)).length > 0">
                                                        <div>
                                                            <template x-for="(achievement, achievementIndex) in getObjectByValue('achievements', localSectionsJson).model" :key="achievementIndex">
                                                                <div class="form-group">
                                                                    <label x-bind:for="`achievement-years-content-${achievementIndex}`" class="d-flex justify-content-between">
                                                                        <span>Achievement Content</span>
                                                                        <template x-if="getObjectByValue('achievements', localSectionsJson).model.length > 1">
                                                                            <div class="card-tools">
                                                                                <button type="button" class="btn btn-tool" @click="removeAchievement(achievementIndex)"><i class="fas fa-times"></i></button>
                                                                            </div>
                                                                        </template>
                                                                    </label>
                                                                    <textarea class="form-control" x-bind:id="`achievement-years-content-${achievementIndex}`" x-bind:value="achievement.model" @input="updateAchievement('achievements', $event.target.value, achievementIndex)" placeholder="Enter achievement content" required></textarea>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                            <div class="card-header">
                                                <h3 class="card-title">Key Qualities</h3>
                                                <div class="card-tools">
                                                    <div class="btn-group">
                                                        <div class="card-tools d-flex">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="quality-display" value="1" @change="updateDisplayToggle('qualities',$event.target.checked)" x-bind:checked="(Object.keys(getObjectByValue('qualities', localSectionsJson)).length > 0)? getObjectByValue('qualities', localSectionsJson).display: true">
                                                                <label class="custom-control-label" for="quality-display">Display?</label>
                                                            </div>
                                                            <button type="button" class="btn btn-tool highlight-section" data-highlight-id="qualities"><i class="fas fa-question-circle"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="qualities-label">Section Title</label>
                                                    <input type="text" class="form-control" id="qualities-label" x-bind:value="(Object.keys(getObjectByValue('qualities', localSectionsJson)).length > 0 )? getObjectByValue('qualities', localSectionsJson).label : ''" @input="updateLabels(index, $event.target.value)" placeholder="Enter label" required>
                                                </div>
                                                <template x-if="Object.keys(getObjectByValue('qualities', localSectionsJson)).length > 0">
                                                    <div>
                                                        <template x-for="(quality, qualityIndex) in getObjectByValue('qualities', localSectionsJson).model" :key="qualityIndex">
                                                            <div class="card card-info">
                                                                <div class="card-header">
                                                                    <h3 class="card-title" x-text="(quality.title != '')? quality.title: 'Title'"></h3>
                                                                    <template x-if="getObjectByValue('qualities', localSectionsJson).model.length > 1">
                                                                        <div class="card-tools">
                                                                            <button type="button" class="btn btn-tool" @click="removeQuality(qualityIndex)">
                                                                                <i class="fas fa-times"></i>
                                                                            </button>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="form-group col-12 col-md-6">
                                                                            <label x-bind:for="`title-${qualityIndex}`">Title</label>
                                                                            <input type="text" class="form-control" x-bind:id="`title-${qualityIndex}`" x-bind:value="quality.title" @input="updateQualities('qualities', $event.target.value, qualityIndex, 'title')" placeholder="Enter title" value="" required>
                                                                        </div>
                                                                        <div class="form-group col-12 col-md-6">
                                                                            <label x-bind:for="`rating-${qualityIndex}`">Rating</label>
                                                                            <select class="form-control" x-bind:id="`rating-${qualityIndex}`" x-bind:value="quality.rating" @input="updateQualities('qualities', $event.target.value, qualityIndex, 'rating')">
                                                                                <option value="1">1</option>
                                                                                <option value="2">2</option>
                                                                                <option value="3">3</option>
                                                                                <option value="4">4</option>
                                                                                <option value="5">5</option>
                                                                                <option value="6">6</option>
                                                                                <option value="7">7</option>
                                                                                <option value="8">8</option>
                                                                                <option value="9">9</option>
                                                                                <option value="10">10</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-block btn-info" @click="addKeyQuality()"><i class="fas fa-plus"></i> Add Quality</button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="button" onclick="stepper.previous()" class="btn btn-primary">Previous</button>
                                            <button type="button" onclick="stepper.next()" class="btn btn-primary">Next</button>
                                        </div>
                                        <!-- /.card-footer -->
                                    </div>
                                    <div id="clientele-testimonial-step" class="content stepper-content" role="tabpanel" data-step-no="6">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                Clientele + Testimonial
                                            </h3>
                                        </div>
                                        <!-- /.card-header -->
                                        <div class="card-body">
                                            <div class="card-header">
                                                <h3 class="card-title">Clientele</h3>
                                                <div class="card-tools">
                                                    <div class="btn-group">
                                                        <div class="card-tools d-flex">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="clientele-display" value="1" @change="updateDisplayToggle('clientele',$event.target.checked)" x-bind:checked="(Object.keys(getObjectByValue('clientele', localSectionsJson)).length > 0)? getObjectByValue('clientele', localSectionsJson).display: true">
                                                                <label class="custom-control-label" for="clientele-display">Display?</label>
                                                            </div>
                                                            <button type="button" class="btn btn-tool highlight-section" data-highlight-id="clientele"><i class="fas fa-question-circle"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="clientele-agency">Agencies</label>
                                                    <input type="text" class="form-control" id="clientele-agency" x-bind:value="(Object.keys(getObjectByValue('clientele', localSectionsJson)).length > 0)? getObjectByValue('clientele', localSectionsJson).model.agencies: ''" @input="updateClientele('clientele', $event.target.value, 'agencies')" placeholder="Enter agency count" value="" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="clientele-companies">Companies</label>
                                                    <input type="text" class="form-control" id="clientele-companies" x-bind:value="(Object.keys(getObjectByValue('clientele', localSectionsJson)).length > 0)? getObjectByValue('clientele', localSectionsJson).model.companies: ''" @input="updateClientele('clientele', $event.target.value, 'companies')" placeholder="Enter company count" value="" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="countries">Countries</label>
                                                    <select class="form-control" id="countries" data-placeholder="Select Countries" x-ref="countries" multiple required>
                                                        @if ($countries->count() > 0)
                                                            @foreach ($countries as $country)
                                                                <option value="{{ $country['value'] }}">{{ $country['label'] }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <h5 class="mt-2">Metrics</h5>
                                                <div class="row">
                                                    <div class="form-group col-12 col-md-6">
                                                        <label for="metric-title">Title</label>
                                                        <input type="text" class="form-control" id="metric-title" x-bind:value="(Object.keys(getObjectByValue('clientele', localSectionsJson)).length > 0)? getObjectByValue('clientele', localSectionsJson).model.metric.title: ''" @input="updateClientele('clientele', $event.target.value, 'metric', 'title')" placeholder="Enter title" value="" required>
                                                    </div>
                                                    <div class="form-group col-12 col-md-6">
                                                        <label for="metric-count">Count</label>
                                                        <input type="text" class="form-control" id="metric-count" x-bind:value="(Object.keys(getObjectByValue('clientele', localSectionsJson)).length > 0)? getObjectByValue('clientele', localSectionsJson).model.metric.count: ''" @input="updateClientele('clientele', $event.target.value, 'metric', 'count')" placeholder="Enter count" value="" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="clientele-overall-rating">Overall Rating</label>
                                                    <select class="form-control" id="clientele-overall-rating" x-bind:value="(Object.keys(getObjectByValue('clientele', localSectionsJson)).length > 0)? getObjectByValue('clientele', localSectionsJson).model.overallRating: ''" @input="updateClientele('clientele', $event.target.value, 'overallRating')">
                                                        <option value="1">1</option>
                                                        <option value="1.5">1.5</option>
                                                        <option value="2">2</option>
                                                        <option value="2.5">2.5</option>
                                                        <option value="3">3</option>
                                                        <option value="3.5">3.5</option>
                                                        <option value="4">4</option>
                                                        <option value="4.5">4.5</option>
                                                        <option value="5">5</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="clientele-customer-satisfaction-rating">Customer Satisfication Rating</label>
                                                    <select class="form-control" id="clientele-customer-satisfaction-rating" x-bind:value="(Object.keys(getObjectByValue('clientele', localSectionsJson)).length > 0)? getObjectByValue('clientele', localSectionsJson).model.clientSatisfaction: ''" @input="updateClientele('clientele', $event.target.value, 'clientSatisfaction')">
                                                        <option value="1">1</option>
                                                        <option value="1.5">1.5</option>
                                                        <option value="2">2</option>
                                                        <option value="2.5">2.5</option>
                                                        <option value="3">3</option>
                                                        <option value="3.5">3.5</option>
                                                        <option value="4">4</option>
                                                        <option value="4.5">4.5</option>
                                                        <option value="5">5</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="card-header">
                                                <h3 class="card-title">Testimonial</h3>
                                                <div class="card-tools">
                                                    <div class="btn-group">
                                                        <div class="card-tools d-flex">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="testimonial-display" value="1" @change="updateDisplayToggle('testimonial',$event.target.checked)" x-bind:checked="(Object.keys(getObjectByValue('testimonial', localSectionsJson)).length > 0)? getObjectByValue('testimonial', localSectionsJson).display: true">
                                                                <label class="custom-control-label" for="testimonial-display">Display?</label>
                                                            </div>
                                                            <button type="button" class="btn btn-tool highlight-section" data-highlight-id="testimonial"><i class="fas fa-question-circle"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="testimonial-label">Section Title</label>
                                                    <input type="text" class="form-control" id="testimonial-label" x-bind:value="(Object.keys(getObjectByValue('testimonial', localSectionsJson)).length > 0)? getObjectByValue('testimonial', localSectionsJson).label: ''" @input="updateLabels('testimonial', $event.target.value)" placeholder="Enter label" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="testimonial-content">Content</label>
                                                    <textarea class="form-control" id="testimonial-content" x-bind:value="(Object.keys(getObjectByValue('testimonial', localSectionsJson)).length > 0)? getObjectByValue('testimonial', localSectionsJson).model: ''" @input="updateSimpleModel('testimonial', $event.target.value)" placeholder="Enter testimonial content" required></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="button" onclick="stepper.previous()" class="btn btn-primary">Previous</button>
                                            <button type="button" onclick="stepper.next()" class="btn btn-primary">Next</button>
                                        </div>
                                        <!-- /.card-footer -->
                                    </div>
                                    <div id="plus-point-fun-fact-step" class="content stepper-content" role="tabpanel" data-step-no="7">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                Plus Points + Fun Fact
                                            </h3>
                                        </div>
                                        <!-- /.card-header -->
                                        <div class="card-body">
                                            <div class="card-header">
                                                <h3 class="card-title">Plus Points</h3>
                                                <div class="card-tools">
                                                    <div class="btn-group">
                                                        <div class="card-tools d-flex">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="plus_points-display" value="1" @change="updateDisplayToggle('plus_points',$event.target.checked)" x-bind:checked="(Object.keys(getObjectByValue('plus_points', localSectionsJson)).length > 0)? getObjectByValue('plus_points', localSectionsJson).display: true">
                                                                <label class="custom-control-label" for="plus_points-display">Display?</label>
                                                            </div>
                                                            <button type="button" class="btn btn-tool highlight-section" data-highlight-id="plus_points"><i class="fas fa-question-circle"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="plus-points-label">Section Title</label>
                                                    <input type="text" class="form-control" id="plus-points-label" x-bind:value="(Object.keys(getObjectByValue('plus_points', localSectionsJson)).length > 0 )? getObjectByValue('plus_points', localSectionsJson).label: ''" @input="updateLabels('plus_points', $event.target.value)" placeholder="Enter label" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="plus-point-content">Content</label>
                                                    <textarea class="form-control" id="plus-point-content" x-bind:value="(Object.keys(getObjectByValue('plus_points', localSectionsJson)).length > 0 )? getObjectByValue('plus_points', localSectionsJson).model: ''" @input="updateSimpleModel('plus_points', $event.target.value)" placeholder="Enter plus point content" required></textarea>
                                                </div>
                                            </div>
                                            <div class="card-header">
                                                <h3 class="card-title">Fun Fact</h3>
                                                <div class="card-tools">
                                                    <div class="btn-group">
                                                        <div class="card-tools d-flex">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="fact-display" value="1" @change="updateDisplayToggle('fact',$event.target.checked)" x-bind:checked="(Object.keys(getObjectByValue('fact', localSectionsJson)).length > 0)? getObjectByValue('fact', localSectionsJson).display: true">
                                                                <label class="custom-control-label" for="fact-display">Display?</label>
                                                            </div>
                                                            <button type="button" class="btn btn-tool highlight-section" data-highlight-id="fact"><i class="fas fa-question-circle"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="fact-label">Section Title</label>
                                                    <input type="text" class="form-control" id="fact-label" x-bind:value="(Object.keys(getObjectByValue('fact', localSectionsJson)).length > 0)? getObjectByValue('fact', localSectionsJson).label: ''" @input="updateLabels('fact', $event.target.value)" placeholder="Enter label" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="fact-content">Content</label>
                                                    <textarea class="form-control" id="fact-content" x-bind:value="(Object.keys(getObjectByValue('fact', localSectionsJson)).length > 0)? getObjectByValue('fact', localSectionsJson).model: ''" @input="updateSimpleModel('fact', $event.target.value)" placeholder="Enter fun fact content" required></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="button" onclick="stepper.previous()" class="btn btn-primary">Previous</button>
                                            <button type="button" onclick="stepper.next()" class="btn btn-primary">Next</button>
                                        </div>
                                        <!-- /.card-footer -->
                                    </div>
                                    <div id="masters-step" class="content stepper-content" role="tabpanel" data-step-no="8">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                Masters
                                            </h3>
                                        </div>
                                        <!-- /.card-header -->
                                        <div class="card-body">
                                            <div class="card-header">
                                                <div class="card-title">ESPs</div>
                                                <div class="card-tools">
                                                    <div class="btn-group">
                                                        <div class="card-tools d-flex">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="esp-display" value="1" @change="updateDisplayToggle('esp',$event.target.checked)" x-bind:checked="(Object.keys(getObjectByValue('esp', localSectionsJson)).length > 0)? getObjectByValue('esp', localSectionsJson).display: true">
                                                                <label class="custom-control-label" for="esp-display">Display?</label>
                                                            </div>
                                                            <button type="button" class="btn btn-tool highlight-section" data-highlight-id="esp"><i class="fas fa-question-circle"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="esp">ESPs</label>
                                                    <select class="form-control" id="esp" data-placeholder="Select ESP" x-ref="esp" multiple required>
                                                        @if ($espModel->count() > 0)
                                                            @foreach ($espModel as $esp)
                                                                <option value="{{ $esp->id }}">{{ $esp->title }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="card-header">
                                                <div class="card-title">PMS</div>
                                                <div class="card-tools">
                                                    <div class="btn-group">
                                                        <div class="card-tools d-flex">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="pms-display" value="1" @change="updateDisplayToggle('pms',$event.target.checked)" x-bind:checked="(Object.keys(getObjectByValue('pms', localSectionsJson)).length > 0)? getObjectByValue('pms', localSectionsJson).display: true">
                                                                <label class="custom-control-label" for="pms-display">Display?</label>
                                                            </div>
                                                            <button type="button" class="btn btn-tool highlight-section" data-highlight-id="pms"><i class="fas fa-question-circle"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="pms">PMS</label>
                                                    <select class="form-control" id="pms" data-placeholder="Select PMS" x-ref="pms" multiple required>
                                                        @if ($pmsModel->count() > 0)
                                                            @foreach ($pmsModel as $pms)
                                                                <option value="{{ $pms->id }}">{{ $pms->title }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="card-header">
                                                <div class="card-title">Frameworks</div>
                                                <div class="card-tools">
                                                    <div class="btn-group">
                                                        <div class="card-tools d-flex">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" id="frameworks-display" value="1" @change="updateDisplayToggle('frameworks',$event.target.checked)" x-bind:checked="(Object.keys(getObjectByValue('frameworks', localSectionsJson)).length > 0)? getObjectByValue('frameworks', localSectionsJson).display: true">
                                                                <label class="custom-control-label" for="frameworks-display">Display?</label>
                                                            </div>
                                                            <button type="button" class="btn btn-tool highlight-section" data-highlight-id="frameworks"><i class="fas fa-question-circle"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="frameworks">Frameworks</label>
                                                    <select class="form-control" id="frameworks" data-placeholder="Select Frameworks" x-ref="frameworks" multiple required>
                                                        @if ($frameworkModel->count() > 0)
                                                            @foreach ($frameworkModel as $framework)
                                                                <option value="{{ $framework->id }}">{{ $framework->title }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="button" onclick="stepper.previous()" class="btn btn-primary">Previous</button>
                                            <button type="button" onclick="stepper.next()" class="btn btn-primary">Next</button>
                                        </div>
                                        <!-- /.card-footer -->
                                    </div>
                                    <div id="gallery-step" class="content stepper-content" role="tabpanel" data-step-no="9">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                Gallery
                                            </h3>
                                            <div class="card-tools">
                                                <div class="btn-group">
                                                    <div class="card-tools">
                                                        <button type="button" class="btn btn-tool highlight-section" data-highlight-id="gallery"><i class="fas fa-question-circle"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.card-header -->
                                        <div class="card-body">
                                            <template x-if="$store.previewRenderJson.details.to_erase_gallery_image_hashes.length > 0">
                                                <div>
                                                    <template x-for="(imageHash, imageIndex) in $store.previewRenderJson.details.to_erase_gallery_image_hashes" :key="imageIndex">
                                                        <input type="text" name="to_erase_gallery_image_hashes[]" x-bind:value="imageHash" hidden />
                                                    </template>
                                                </div>
                                            </template>
                                            <div class="dropzone" id="gallery"></div>
                                        </div>

                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="button" onclick="stepper.previous()" class="btn btn-primary">Previous</button>
                                            <button type="button" onclick="stepper.next()" class="btn btn-primary">Next</button>
                                        </div>
                                        <!-- /.card-footer -->
                                    </div>
                                    <div id="hobby-step" class="content stepper-content" role="tabpanel" data-step-no="10">
                                        <div class="card-header">
                                            <h3 class="card-title">Hobbies</h3>
                                            <div class="card-tools">
                                                <div class="btn-group">
                                                    <div class="card-tools d-flex">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input" id="hobbies-display" value="1" @change="updateDisplayToggle('hobbies', $event.target.checked)" x-bind:checked="(Object.keys(getObjectByValue('hobbies', localSectionsJson)).length > 0)? getObjectByValue('hobbies', localSectionsJson).display : true">
                                                            <label class="custom-control-label" for="hobbies-display">Display?</label>
                                                        </div>
                                                        <button type="button" class="btn btn-tool highlight-section" data-highlight-id="hobbies"><i class="fas fa-question-circle"></i></button>
                                                        <button type="button" class="btn btn-tool" @click="addHobby()">
                                                            <i class="fas fa-plus"></i>&nbsp;&nbsp; Add hobby
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.card-header -->
                                        <div class="card-body">
                                            <template x-if="(Object.keys(getObjectByValue('hobbies', localSectionsJson)).length > 0) ">
                                                <div>
                                                    <template x-for="(hobby, index) in getObjectByValue('hobbies', localSectionsJson).model" :key="index">
                                                        <div class="form-group">
                                                            <div class="mb-2 d-flex align-items-center justify-content-between">
                                                                <label x-bind:for="`hobby${index}`" class="mb-0" x-text="(hobby.title != '')? hobby.title: 'Hobby'">Hobby</label>
                                                                <template x-if="getObjectByValue('hobbies', localSectionsJson).model.length > 1">
                                                                    <div class="card-tools">
                                                                        <button type="button" class="btn btn-tool" @click="removeHobby(index)"><i class="fas fa-times"></i></button>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                            <input type="text" class="form-control" x-bind:id="`hobby${index}`" placeholder="Enter hobby" x-bind:value="hobby.title" @input="updateHobbiesModel('hobbies', index, $event.target.value)" required>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>

                                        </div>

                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="button" onclick="stepper.previous()" class="btn btn-primary">Previous</button>
                                            <button type="button" onclick="stepper.next()" class="btn btn-primary">Next</button>
                                        </div>
                                        <!-- /.card-footer -->
                                    </div>
                                    <div id="custom-step" class="content stepper-content" role="tabpanel" data-step-no="11">
                                        <div class="card-header">
                                            <h3 class="card-title">Custom Sections</h3>
                                            <div class="card-tools">
                                                <div class="btn-group">
                                                    <div class="card-tools d-flex">
                                                        <button type="button" class="btn btn-tool" data-toggle="modal" data-target="#custom-section-catalogue">
                                                            <i class="fas fa-plus"></i>&nbsp;&nbsp; Add Section
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.card-header -->
                                        <div class="card-body">
                                            <template x-if="getCustomSections(localSectionsJson).length > 0">
                                                <div>
                                                    <template x-for="(section, index) in getCustomSections(localSectionsJson)" :key="index">
                                                        <div class="card card-info">
                                                            <div class="card-header ">
                                                                <h3 class="mb-0 card-title" x-text="section.label">Custom Section</h3>
                                                                <div class="card-tools">
                                                                    <div class="btn-group">
                                                                        <div class="card-tools d-flex">
                                                                            <div class="custom-control custom-switch">
                                                                                <input type="checkbox" class="custom-control-input" x-bind:id="`custom-section-display-${section.value}`" value="1" @change="updateDisplayToggle(section.value,$event.target.checked)" x-bind:checked="section.display">
                                                                                <label class="custom-control-label" x-bind:for="`custom-section-display-${section.value}`">Display?</label>
                                                                            </div>
                                                                            <button type="button" class="btn btn-tool" @click="removeCustomSection(section.value)"><i class="fas fa-times"></i></button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="form-group">
                                                                    <label x-bind:for="`custom-section-label-${section.value}`">Section Title</label>
                                                                    <input type="text" class="form-control" x-bind:id="`custom-section-label-${section.value}`" x-bind:value="section.label" @input="updateLabels(section.value, $event.target.value)" placeholder="Enter label" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label x-bind:for="`custom-section-bg-${section.value}`">Background Colour</label>
                                                                    <input type="color" class="form-control" x-bind:id="`custom-section-bg-${section.value}`" x-bind:value="section.attrs.bg" @change="updateCustomSectionAttr(section.value, 'bg', $event.target.value)" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label x-bind:for="`custom-section-text-${section.value}`">Text Colour</label>
                                                                    <input type="color" class="form-control" x-bind:id="`custom-section-text-${section.value}`" x-bind:value="section.attrs.text" @change="updateCustomSectionAttr(section.value, 'text', $event.target.value)" required>
                                                                </div>
                                                                <!-- content -->
                                                                <template x-if="section.customType == 'text'">
                                                                    <div class="form-group">
                                                                        <label x-bind:for="`custom-section-content-${section.value}`">Content</label>
                                                                        <textarea class="form-control" x-bind:id="`custom-section-content-${section.value}`" x-bind:value="section.model" @input="updateSimpleModel(section.value, $event.target.value)" placeholder="Enter section content" required></textarea>
                                                                    </div>
                                                                </template>
                                                                <template x-if="section.customType == 'bullet-text'">
                                                                    <div>
                                                                        <div class="card-header">
                                                                            <h3 class="card-title">
                                                                                Bullet Items
                                                                            </h3>
                                                                            <div class="card-tools">
                                                                                <div class="btn-group">
                                                                                    <div class="card-tools d-flex">
                                                                                        <button type="button" class="btn btn-tool text-black-50" @click="addCustomSectionBulletItem(section.value)">
                                                                                            <i class="fas fa-plus"></i>&nbsp;&nbsp; Add Item
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <template x-for="(bulletText, bulletTextIndex) in section.model" :key="bulletTextIndex">
                                                                                <div class="form-group">
                                                                                    <div class="mb-2 d-flex align-items-center justify-content-between">
                                                                                        <label x-bind:for="`custom-bullet-text-${index}-${bulletTextIndex}`" class="mb-0" x-text="(bulletText.model != '')? bulletText.model: 'Content'">Content</label>
                                                                                        <template x-if="section.model.length > 1">
                                                                                            <div class="card-tools">
                                                                                                <button type="button" class="btn btn-tool text-black-50" @click="removeCustomSectionBulletItem(section.value, bulletTextIndex)"><i class="fas fa-times"></i></button>
                                                                                            </div>
                                                                                        </template>
                                                                                    </div>
                                                                                    <input type="text" class="form-control" x-bind:id="`custom-bullet-text-${index}-${bulletTextIndex}`" placeholder="Enter content" x-bind:value="bulletText.model" @input="updateCustomSectionBulletItem(section.value, bulletTextIndex, $event.target.value)" required>
                                                                                </div>
                                                                            </template>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                                <!-- /content -->
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                            <template x-if="getCustomSections(localSectionsJson).length == 0">
                                                <p class="text-center">No Custom Sections</p>
                                            </template>
                                        </div>

                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="button" onclick="stepper.previous()" class="btn btn-primary">Previous</button>
                                            <button type="submit" class="btn btn-success form-submit" :disabled="!$store.previewRenderJson.canRenderPreview">Save</button>
                                            <a href="{{ route('admin.portfolio.index') }}" class="mx-1 btn">Cancel</a>
                                        </div>
                                        <!-- /.card-footer -->
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>
                            <!-- /.col -->
                            <div class="col-12 col-md-6" x-data="buildPreviews()">
                                <div class="card position-sticky" style="top: 64px;">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            Preview
                                        </h3>
                                        <div class="card-tools">
                                            <ul class="ml-auto nav nav-pills">
                                                <li class="nav-item">
                                                    <button type="submit" class="mx-2 btn btn-small btn-success form-submit" :disabled="!$store.previewRenderJson.slugInputValid">
                                                        <i class="fas fa-save"></i> Save
                                                    </button>
                                                    <button type="button" @click="renderPreview('{{ route('admin.portfolio.portfolio-preview.show', $portfolioCopy->temp_version_id) }}')" class="mx-2 btn btn-small btn-primary" :disabled="!$store.previewRenderJson.canRenderPreview">
                                                        <i class="fas fa-eye"></i> Preview
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-header">
                                        <div class="d-flex justify-content-center">
                                            <ul class="devices nav nav-pills">
                                                <li>
                                                    <button type="button" @click="switchPreviewDevice('desktop')" class="mx-2 btn btn-small" :class="($store.previewRenderJson.previewDevice == 'desktop')? 'btn-primary': 'btn-outline-secondary'" :disabled="!$store.previewRenderJson.canRenderPreview">
                                                        <i class="fas fa-desktop"></i> Desktop
                                                    </button>
                                                </li>
                                                <li>
                                                    <button type="button" @click="switchPreviewDevice('tablet')" class="mx-2 btn btn-small" :class="($store.previewRenderJson.previewDevice == 'tablet')? 'btn-primary': 'btn-outline-secondary'" :disabled="!$store.previewRenderJson.canRenderPreview">
                                                        <i class="fas fa-tablet"></i> Tablet
                                                    </button>
                                                </li>
                                                <li>
                                                    <button type="button" @click="switchPreviewDevice('mobile')" class="mx-2 btn btn-small" :class="($store.previewRenderJson.previewDevice == 'mobile')? 'btn-primary': 'btn-outline-secondary'" :disabled="!$store.previewRenderJson.canRenderPreview">
                                                        <i class="fas fa-mobile"></i> Mobile
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="p-0 card-body h-100 preview">
                                        <template x-if="$store.previewRenderJson.canRenderPreview">
                                            <div class="device h-100" :class="`device-${$store.previewRenderJson.previewDevice}`">
                                                <iframe src="{{ route('admin.portfolio.portfolio-preview.show', $portfolioCopy->temp_version_id) }}" class="w-100 preview-iframe" id="preview-iframe" frameborder="0"></iframe>
                                            </div>
                                        </template>
                                        <template x-if="!$store.previewRenderJson.canRenderPreview">
                                            <div class="px-2 py-5">
                                                <p class="text-center">All details are required for generating a preview</p>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.row -->
        </div>
        <div id="preview-template" style="display: none;">
            <div class="dz-preview dz-file-preview">
                <div class="dz-image"><img data-dz-thumbnail /></div>
                <div class="dz-details">
                    <div class="dz-size"><span data-dz-size></span></div>
                    <div class="dz-filename"><span data-dz-name></span></div>
                </div>
                <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                <div class="dz-error-message"><span data-dz-errormessage></span></div>
                <div class="dz-success-mark">
                    <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">
                        <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage">
                            <path d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z" id="Oval-2" stroke-opacity="0.198794158" stroke="#747474" fill-opacity="0.816519475" fill="#FFFFFF" sketch:type="MSShapeGroup"></path>
                        </g>
                    </svg>
                </div>
                <div class="dz-error-mark">
                    <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">
                        <defs></defs>
                        <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage">
                            <g id="Check-+-Oval-2" sketch:type="MSLayerGroup" stroke="#747474" stroke-opacity="0.198794158" fill="#FFFFFF" fill-opacity="0.816519475">
                                <path d="M32.6568542,29 L38.3106978,23.3461564 C39.8771021,21.7797521 39.8758057,19.2483887 38.3137085,17.6862915 C36.7547899,16.1273729 34.2176035,16.1255422 32.6538436,17.6893022 L27,23.3431458 L21.3461564,17.6893022 C19.7823965,16.1255422 17.2452101,16.1273729 15.6862915,17.6862915 C14.1241943,19.2483887 14.1228979,21.7797521 15.6893022,23.3461564 L21.3431458,29 L15.6893022,34.6538436 C14.1228979,36.2202479 14.1241943,38.7516113 15.6862915,40.3137085 C17.2452101,41.8726271 19.7823965,41.8744578 21.3461564,40.3106978 L27,34.6568542 L32.6538436,40.3106978 C34.2176035,41.8744578 36.7547899,41.8726271 38.3137085,40.3137085 C39.8758057,38.7516113 39.8771021,36.2202479 38.3106978,34.6538436 L32.6568542,29 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z" id="Oval-2" sketch:type="MSShapeGroup"></path>
                            </g>
                        </g>
                    </svg>
                </div>
            </div>
        </div>
        <div class="modal fade" id="custom-section-catalogue" tabindex="-1" aria-modal="true" role="dialog" style="padding-right: 17px">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Custom Sections</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <button type="button" class="btn-primary btn" data-dismiss="modal" @click="addCustomSection('text')">
                            Text with background color
                        </button>
                        <button type="button" class="btn-primary btn" data-dismiss="modal" @click="addCustomSection('bullet-text')">
                            Bullet point text with background color
                        </button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
@endsection
