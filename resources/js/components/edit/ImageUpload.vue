<template>
    <div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 fa-3x" v-if="image">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                        <div class="col">
                            <div class="input-group mb-3">
                                <div class="custom-file">
                                    <input type="file" ref="file" v-on:change="onImageChange" class="custom-file-input"
                                        id="inputGroupFile02" @change="uploadImage">
                                    <label class="custom-file-label" for="inputGroupFile02"
                                        aria-describedby="inputGroupFileAddon02">Choose an image</label>
                                </div>
                                <!-- <div class="input-group-append">
                                    <button class="btn btn-outline-success" id="inputGroupFileAddon02" @click="uploadImage">Upload</button>
                                </div> -->
                            </div>
                        </div>

                    </div>
                   <div class="row alert alert-danger mt-2" role="alert" v-if="errorText">
                        <strong>Error: </strong> {{ errorText }}
                    </div>
                </div>
                 
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                image: '',
                errorText: null
            }
        },
        methods: {
            onImageChange(e) {
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.createImage(files[0]);
            },
            createImage(file) {
                let reader = new FileReader();
                let vm = this;
                reader.onload = (e) => {
                    vm.image = e.target.result;
                };
                reader.readAsDataURL(file);
            },
            uploadImage() {
                let data = new FormData()
                this.errorText = null;
                data.append('image', this.$refs.file.files[0])
                axios.post('/creator/image/store', data).then(response => {
                    if (response.data.success) {
                        this.$emit("imageuploaded", response.data.image);    
                        
                    }
                })
                .catch((error) => {

                    this.errorText = error.response.data.error.image;
                });
            }
        }
    }
</script>