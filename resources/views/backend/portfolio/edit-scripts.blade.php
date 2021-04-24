<script>
    // State management via spruce
    Spruce.store('sectionsStore', {
        sections: [
            {
                label: "Profile",
                value: "profile",
                model: "Profile sample content",
                display: true,
            },
            {
                label: "Years of experience",
                value: "experience",
                model: [
                    {
                        title: "Job title",
                        company: "Company Name",
                        start: "Start Date eg. Jan 2001",
                        end : "End date eg. Jan 2020",
                        content: "Experience sample content 1"
                    }
                ],
                display: true
            },
            {
                label: "Education",
                value: "education",
                model: [
                    {
                        course: "Course Name",
                        institution: "Institution Name",
                        start: "Start Date eg. Jan 2001",
                        end : "End date eg. Jan 2020",
                        content: "Education sample content 1"
                    }
                ],
                display: true,
            },
            {
                label: "Achievements",
                value: "achievements",
                model: [
                    {model: "Achievement sample content 1"},
                    {model: "Achievement sample content 2"}
                ],
                display: true,
            },
            {
                label: "Key qualities",
                value: "qualities",
                model: [{
                    title: "Sample Quality",
                    rating: "5"
                }],
                display: true,
            },
            {
                label: "Clientele",
                value: "clientele",
                model: {
                    agencies: "100",
                    companies: "100",
                    countries: ['us'],
                    metric: {
                        title: "Template",
                        count: "500"
                    },
                    overallRating: 3.5,
                    clientSatisfaction: 3.5,
                },
                display: true,
            },
            {
                label: "Plus points",
                value: "plus_points",
                model: "Plus point sample content",
                display: true,
            },
            {
                label: "Latest testimonial",
                value: "testimonial",
                model: "Testimonial sample content",
                display: true,
            },
            {
                label: "Fun fact",
                value: "fact",
                model: "Fun fact sample content",
                display: true,
            },
            {
                label: "Expertise in ESPs",
                value: "esp",
                model: [],
                display: true,
            },
            {
                label: "Familiar with PMS tool",
                value: "pms",
                model: [],
                display: true,
            },
            {
                label: "Worked on framework",
                value: "frameworks",
                model: [],
                display: true,
            },
            {
                label: "Hobbies",
                value: "hobbies",
                model : [
                    {
                        title: "Your Hobbies"
                    }
                ],
                display: true,
            },
        ],
    });

    Spruce.store('previewRenderJson', {
        details: {
            to_erase_gallery_image_hashes: [],
            gallery_image_hashes: [],
            profile_image_hash : "",
        },
        canRenderPreview: false,
        slugInputValid: true,
        hasPreviewFirstLoaded: false,
        previewDevice: 'desktop',
    });


    // Model By Value
    function getObjectByValue(valueToFind, array) {
        let arr = array.filter(function(item) {
            return item.value == valueToFind; // Get only elements, which have such a key
        });
        return arr.length > 0? arr[0]: {};
    }

    // All Custom Sections
    function getCustomSections(array) {
        let arr = array.filter(function(item) {
            return item.hasOwnProperty('type') && item.type == "custom";
        });
        return arr;
    }
    
    // Alpinejs
    function sectionData() {
        return {
            localSectionsJson: [],
            setNewLocalJson(newJson){
                this.localSectionsJson = newJson;
            },
            sectionTypesInit() {
                let incomingSectionJson = @json(old('section_data', $portfolioCopy->section_data));
                if (incomingSectionJson != null && incomingSectionJson != "")
                {
                    let parsedSectionJson = Spruce.store('sectionsStore').sections;

        
                    parsedSectionJson = [];
                    for (const json of JSON.parse(incomingSectionJson)) {
                        parsedSectionJson.push(json);
                    }

                    Spruce.reset('sectionsStore', {
                        sections: parsedSectionJson,
                    });

                    this.setNewLocalJson(parsedSectionJson)
                    buildPreviews().onValueEntry('section_data', JSON.stringify(parsedSectionJson), true)

                    this.$nextTick(() => {
                        for (const json of this.localSectionsJson) {
                            this.initSelect2(json.value, json.model)
                        }
                    });
                }
                else {

                    for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                        if(item.value == 'esp'){
                            let selectedEsp = {{ ($espModel->count() > 0 ) ? $espModel->first()->id: '' }};
                            let updatedItem = this.$store.sectionsStore.sections[index];
                            updatedItem = deepClone(updatedItem);
                            updatedItem.model.push(selectedEsp);
                            this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                        } else if(item.value == 'pms'){
                            let selectedPms = {{ ($pmsModel->count() > 0 ) ? $pmsModel->first()->id: '' }};
                            let updatedItem = this.$store.sectionsStore.sections[index];
                            updatedItem = deepClone(updatedItem);
                            updatedItem.model.push(selectedPms);
                            this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                        
                        } else if(item.value == 'frameworks'){
                            let selectedFramework = {{ ($frameworkModel->count() > 0 ) ? $frameworkModel->first()->id: '' }};
                            let updatedItem = this.$store.sectionsStore.sections[index];
                            updatedItem = deepClone(updatedItem);
                            updatedItem.model.push(selectedFramework);
                            this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                        }
                    }

                    let store = Spruce.store('sectionsStore');

                    this.setNewLocalJson(store.sections)
                    buildPreviews().onValueEntry('section_data', JSON.stringify(store.sections));

                    this.$nextTick(() => {
                        for (const json of this.localSectionsJson) {
                            this.initSelect2(json.value, json.model)
                        }
                    });
                }

                // Add watcher
                Spruce.watch('sectionsStore.sections', (value) => {
                    let store = Spruce.store('sectionsStore').sections;
                    buildPreviews().onValueEntry('section_data', JSON.stringify(store));
                    this.setNewLocalJson(deepClone(store));
                });
            },
            initSelect2(value, model) {
                let store = this.$store.sectionsStore;
                if (value == 'clientele') {
                    let select2 = $(this.$refs.countries);
                    setTimeout(() => {
                        select2.select2({
                            theme: 'bootstrap4',
                            multiple:true,
                        });
                    }, 3000);
                    select2.val(model.countries).trigger('change');
                    select2.on('select2:select', (event) => {
                        for (const [index, section] of this.$store.sectionsStore.sections.entries()) {
                            if (section.value == 'clientele') {
                                let sectionModel = deepClone(this.$store.sectionsStore.sections[index]);
                                sectionModel.model.countries.push(event.params.data.id)
                                store.sections.splice(index, 1, sectionModel);
                            }
                        }
                    });
                    select2.on('select2:unselect', (event) => {
                        for (const [index, section] of this.$store.sectionsStore.sections.entries()) {
                            if (section.value == 'clientele') {
                                let sectionModel = deepClone(this.$store.sectionsStore.sections[index]);
                                sectionModel.model.countries = section.model.countries.filter((v) => {
                                    return v != event.params.data.id
                                });
                                store.sections.splice(index, 1, sectionModel);
                            }
                        }
                    });

                } else if (value == 'esp') {
                    let select2 = $(this.$refs.esp);
                    select2.val(model).trigger('change');
                    setTimeout(() => {
                        select2.select2({
                            theme: 'bootstrap4',
                            multiple:true,
                        });
                    }, 3000);

                    select2.on('select2:select', (event) => {
                        for (const [index, section] of this.$store.sectionsStore.sections.entries()) {
                            if (section.value == 'esp') {
                                let sectionModel = deepClone(this.$store.sectionsStore.sections[index]);
                                sectionModel.model.push(event.params.data.id)
                                store.sections.splice(index, 1, sectionModel);
                            }
                        }
                    });
                    select2.on('select2:unselect', (event) => {
                        for (const [index, section] of this.$store.sectionsStore.sections.entries()) {
                            if (section.value == 'esp') {
                                let sectionModel = deepClone(this.$store.sectionsStore.sections[index]);
                                sectionModel.model= section.model.filter((v) => {
                                    return v != event.params.data.id
                                });
                                store.sections.splice(index, 1, sectionModel);
                            }
                        }
                    });
                } else if (value == 'pms') {
                    let select2 = $(this.$refs.pms);
                    select2.val(model).trigger('change');
                    setTimeout(() => {
                        select2.select2({
                            theme: 'bootstrap4',
                            multiple:true,
                        });
                    }, 3000);

                    select2.on('select2:select', (event) => {
                        for (const [index, section] of this.$store.sectionsStore.sections.entries()) {
                            if (section.value == 'pms') {
                                let sectionModel = deepClone(this.$store.sectionsStore.sections[index]);
                                sectionModel.model.push(event.params.data.id)
                                store.sections.splice(index, 1, sectionModel);
                            }
                        }
                    });
                    select2.on('select2:unselect', (event) => {
                        for (const [index, section] of this.$store.sectionsStore.sections.entries()) {
                            if (section.value == 'pms') {
                                let sectionModel = deepClone(this.$store.sectionsStore.sections[index]);
                                sectionModel.model= section.model.filter((v) => {
                                    return v != event.params.data.id
                                });
                                store.sections.splice(index, 1, sectionModel);
                            }
                        }
                    });
                } else if (value == 'frameworks') {
                    let select2 = $(this.$refs.frameworks);
                    select2.val(model).trigger('change');
                    setTimeout(() => {
                        select2.select2({
                            theme: 'bootstrap4',
                            multiple:true,
                        });
                    }, 3000);
                    select2.on('select2:select', (event) => {
                        for (const [index, section] of this.$store.sectionsStore.sections.entries()) {
                            if (section.value == 'frameworks') {
                                let sectionModel = deepClone(this.$store.sectionsStore.sections[index]);
                                sectionModel.model.push(event.params.data.id)
                                store.sections.splice(index, 1, sectionModel);
                            }
                        }
                    });
                    select2.on('select2:unselect', (event) => {
                        for (const [index, section] of this.$store.sectionsStore.sections.entries()) {
                            if (section.value == 'frameworks') {
                                let sectionModel = deepClone(this.$store.sectionsStore.sections[index]);
                                sectionModel.model= section.model.filter((v) => {
                                    return v != event.params.data.id
                                });
                                store.sections.splice(index, 1, sectionModel);
                            }
                        }
                    });
                }
            }
        }
    }

    function sectionDataManipulate() {
        return {
            addExperience() {
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == 'experience'){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);

                        let newExperience = {
                            title: "Job title",
                            company: "Company Name",
                            start: "Start Date eg. Jan 2001",
                            end : "End date eg. Jan 2020",
                            content: "Experience sample content"
                        }
                        updatedItem.model.push(newExperience);
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },
            removeExperience(experienceIndex) {
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == 'experience'){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);
                        updatedItem.model.splice(experienceIndex, 1);
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },
            addExperienceContent(experienceIndex){
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == 'experience'){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);
                        updatedItem.model[experienceIndex].content.push({model: "sample content"});
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },
            removeExperienceContent(experienceIndex, experienceContentIndex){
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == 'experience'){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);
                        updatedItem.model[experienceIndex].content.splice(experienceContentIndex, 1);
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },

            addEducation() {
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == 'education'){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);

                        let newEducation = {
                            course: "Course Name",
                            institution: "Institution Name",
                            start: "Start Date eg. Jan 2001",
                            end : "End date eg. Jan 2020",
                            content: "Education sample content"
                        }
                        updatedItem.model.push(newEducation);
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },
            removeEducation(educationIndex) {
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == 'education'){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);
                        updatedItem.model.splice(educationIndex, 1);
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },
            addAchievement() {
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == 'achievements'){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);
                        updatedItem.model.push({model: "Your achievement"},);
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },
            removeAchievement(achievementIndex) {
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == 'achievements'){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);
                        updatedItem.model.splice(achievementIndex, 1);
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },
            addKeyQuality() {
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == 'qualities'){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);
                        updatedItem.model.push({
                            title: "",
                            rating: "5"
                        });
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },
            removeQuality(qualityIndex) {
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == 'qualities'){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);
                        updatedItem.model.splice(qualityIndex, 1);
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },
            addHobby() {
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == 'hobbies'){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);
                        updatedItem.model.push({
                            title: "Your Hobbies"
                        });
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },
            removeHobby(hobbyIndex) {
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == 'hobbies'){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);
                        updatedItem.model.splice(hobbyIndex, 1);
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },
            addCustomSection(type) {
                let model = "Your Custom Content";

                if(type=='bullet-text'){
                    model = [{model: "Your Custom Content"}];
                }
                let randomId = Math.floor(Math.random() * 10000 + 1);
                let value = "customSection"+randomId;
                let sample = {
                    label: "Your Custom Section",
                    type : "custom",
                    value: value,
                    customType : type,
                    attrs: { 
                        "bg": "#ffffff",
                        "text": "#212121"
                    },
                    model : model,
                    display: true,
                };

                this.$store.sectionsStore.sections.push(sample);
            },
            removeCustomSection(sectionValue) {
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == sectionValue){
                        this.$store.sectionsStore.sections.splice(index, 1)
                    }
                }
            },
            addCustomSectionBulletItem(sectionValue){
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == sectionValue){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);
                        updatedItem.model.push({model: "Your Custom Content"});
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },
            removeCustomSectionBulletItem(sectionValue, bulletTextIndex){
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == sectionValue){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);
                        updatedItem.model.splice(bulletTextIndex, 1);
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },

            // Update Model value of each section
            updateDisplayToggle(sectionKey, value){
                 for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == sectionKey){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);
                        updatedItem.display = value
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },
            updateLabels(sectionKey, value){
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == sectionKey){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);
                        updatedItem.label = value
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },
            updateSimpleModel(sectionKey, value){
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == sectionKey){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);
                        updatedItem.model = value;
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },
            updateExperience(sectionKey, modelIndex, modelSubkey, value){
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == sectionKey){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);
                        updatedItem.model[modelIndex][modelSubkey] = value;
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },
            updateExperienceContent(sectionKey, modelIndex, experienceContentIndex, value){
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == sectionKey){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);
                        updatedItem.model[modelIndex].content[experienceContentIndex].model = value;
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },
            updateEducation(sectionKey, modelIndex, modelSubkey, value){
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == sectionKey){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);
                        updatedItem.model[modelIndex][modelSubkey] = value;
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },
            updateAchievement(sectionKey, value, contentIndex){
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == sectionKey){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);
                        updatedItem.model[contentIndex].model = value;
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },
            updateQualities(sectionKey, value, qualityIndex, modelSubkey){
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == sectionKey){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);
                        updatedItem.model[qualityIndex][modelSubkey] = value;
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },
            updateClientele(sectionKey, value, modelSubkey, metricSubKey = null){
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == sectionKey){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);
                        if(modelSubkey == 'metric'){
                            updatedItem.model[modelSubkey][metricSubKey] = value
                        } else {
                            updatedItem.model[modelSubkey] = value
                        }
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },
            updateHobbiesModel(sectionKey, hobbyIndex, value){
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == sectionKey){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);
                        updatedItem.model[hobbyIndex].title = value;
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },
            updateCustomSectionAttr(customSectionKey, attrKey, value){
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == customSectionKey){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);
                        updatedItem.attrs[attrKey] = value;
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },
            updateCustomSectionBulletItem(sectionValue, bulletTextIndex, value){
                for (const [index, item] of this.$store.sectionsStore.sections.entries()) {
                    if(item.value == sectionValue){
                        let updatedItem = this.$store.sectionsStore.sections[index];
                        updatedItem = deepClone(updatedItem);
                        updatedItem.model[bulletTextIndex].model = value;
                        this.$store.sectionsStore.sections.splice(index, 1, updatedItem)
                    }
                }
            },
        }
    }

    function checkSlugValue(){
        return {
            slugValue : "",
            slugInputValid: true,
            slugInputValidFeedback: "Slug input cannot be empty",
            onSlugInput (event){
                let value = event.target.value;
                this.slugValue = value;
                this.slugInputValid = (value != "" && value != null);

                if(this.slugInputValid){
                    this.checkValue();
                }

            },
            checkValue(){
                if(this.slugInputValid){
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('admin.portfolio.edit.slug-check', $portfolioCopy->portfolio_id) }}",
                        data: { "_token": "{{ csrf_token() }}", slug : this.slugValue },
                        success: (data) => {
                            if(data.success){
                                this.slugInputValid = true;
                                this.slugInputValidFeedback = "Slug input cannot be empty";
                                let store = Spruce.store('previewRenderJson');
                                store.slugInputValid = true;
                            }   else{
                                this.slugInputValid = false;
                                this.slugInputValidFeedback = data.error;
                                let store = Spruce.store('previewRenderJson');
                                store.slugInputValid = false;
                            }
                        }
                    });
                }
            }
        }
    }

    function buildPreviews(){
        return {
            isPreviewLoading: false,
            hasPreviewFirstLoaded: false,
            onAppInit(){
                let firstName = "{{ old('first-name', $portfolioCopy->first_name) }}"
                let lastName = "{{ old('last-name', $portfolioCopy->last_name) }}"
                let email = "{{ old('email', $portfolioCopy->email) }}"
                let mobile = "{{ old('mobile', $portfolioCopy->mobile) }}"
                let template = "{{ old('template', isset($portfolioCopy->template_id)? $portfolioCopy->template_id: 1) }}"
                let designation = "{{ old('designation', isset($portfolioCopy->designation)? $portfolioCopy->designation : 'Your Designation') }}"
                let skillLevel = "{{ old('skill_level',  isset($portfolioCopy->skill_level)? $portfolioCopy->skill_level : 'Your Skill Level') }}"
                let activeStatus = "{{ (old('active', $portfolioCopy->active))? '1': '0' }}"

                this.onValueEntry('first-name', firstName)
                this.onValueEntry('last-name', lastName)
                this.onValueEntry('email', email)
                this.onValueEntry('mobile', mobile)
                this.onValueEntry('template', template)
                this.onValueEntry('designation', designation)
                this.onValueEntry('skill_level', skillLevel)
                this.onValueEntry('active', activeStatus)
            },
            onValueEntry(key, value){
                let store = Spruce.store('previewRenderJson');

                if(value != null && value != "" && value.length > 0){
                    store.details[key] = value
                } else {
                    delete store.details[key]
                }

                if(!this.requiredFieldsPresent()){
                    store.canRenderPreview = false;
                } else {
                    store.canRenderPreview = true;
                }
            },
            requiredFieldsPresent(){
                let store = Spruce.store('previewRenderJson');
                
                // Check if required keys are present
                let firstNamePresent = "first-name" in store.details;
                let lastNamePresent = "last-name" in store.details;
                let templatePresent = "template" in store.details;
                let designationPresent = "designation" in store.details;
                let skillLevelPresent = "skill_level" in store.details;
                let sectionDataPresent = "section_data" in store.details;
                let activeStatusPresent = "active" in store.details;

                return (firstNamePresent && lastNamePresent && templatePresent && designationPresent && skillLevelPresent && sectionDataPresent && activeStatusPresent);
            },
            validatePreviewConditions(){
                let store = Spruce.store('previewRenderJson');
                
                if(this.requiredFieldsPresent()){
                    if(!store.hasPreviewFirstLoaded){
                        store.hasPreviewFirstLoaded = true;
                        if(!this.isPreviewLoading){
                            this.isPreviewLoading = true;
                            this.renderPreview();
                        }
                    }
                } else{
                    store.canRenderPreview = false;
                }
            },
            renderPreview(previewUrl){
                let store = Spruce.store('previewRenderJson');
                let json = store.details;

                if(this.requiredFieldsPresent()){
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('admin.portfolio.portfolio-preview.store', $portfolioCopy->temp_version_id) }}",
                        data: { "_token": "{{ csrf_token() }}", ...deepClone(json) },
                        success: function(data) {
                            this.isPreviewLoading = false;

                            if(data.success){

                                let store = Spruce.store('previewRenderJson');
                                let json = store.details;
                                
                                store.canRenderPreview = true;
                                $('#preview-iframe').attr('src', previewUrl);

                                $(".dz-preview").each(function(index, element){
                                    let el = $(element);
                                    if(!el.hasClass("existing-file")){
                                        el.addClass('existing-file');
                                    }
                                })
                            }   else{
                                toastr.error('Error when loading preview')
                            }
                        }
                    });
                }
            },
            switchPreviewDevice(newDevice){
                let store = Spruce.store('previewRenderJson');

                if(store.previewDevice != newDevice){
                    store.previewDevice = newDevice
                }
            },
        }
    }
</script>