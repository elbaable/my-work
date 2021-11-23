<template>
    <div>
        <h3 class="text-center">Edit Partner</h3>
        <div class="row">
            <div class="col-md-12" style="margin:auto;">
                <form @submit.prevent="updateProduct">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" v-model="product.name">
                    </div>
                    <div class="form-group">
                        <label>Photo</label>
                        <input
                          ref="input"
                          type="file"
                          name="photo"
                          accept="image/*"
                          @change="setImage"
                        />

                        <div class="content">
                            <section class="cropper-area">
                                <div class="img-cropper">
                                  <vue-cropper
                                    ref="cropper"
                                    :aspect-ratio="10 / 10"
                                    :src="imgSrc"
                                    preview=".preview"
                                  />
                                </div>
                                <div class="actions">
                                    <a
                                        href="#"
                                        role="button"
                                        @click.prevent="showFileChooser"
                                    >
                                        Upload Image
                                    </a>
                                    <a
                                        href="#"
                                        role="button"
                                        @click.prevent="cropImage"
                                    >
                                        Crop
                                    </a>
                                </div>
                            </section>
                            <section class="preview-area">
                                <p>Preview</p>
                                <div class="preview" />
                                <p>Cropped Image</p>
                                <div class="cropped-image">
                                  <img
                                    v-if="cropImg"
                                    :src="cropImg"
                                    alt="Cropped Image"
                                  />
                                  <div v-else class="crop-placeholder" />
                                </div>
                            </section>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</template>
 
<script>
    import VueCropper from 'vue-cropperjs';
    import 'cropperjs/dist/cropper.css';
    export default {
        data() {
            return {
                product: {},
                imgSrc: '/1.jpg',
                cropImg: '',
                data: null,
                name: ''
            }
        },
        created() {
            this.axios
                .get(`http://localhost:8000/api/partners/${this.$route.params.id}`)
                .then((res) => {
                    this.product = res.data;
                });
        },
        methods: {
            cropImage() {
                // get image data for post processing, e.g. upload or setting image src
                this.cropImg = this.$refs.cropper.getCroppedCanvas().toDataURL();
            },
            updateProduct() {
                const link = this
                const name = this.product.name
                this.$refs.cropper.getCroppedCanvas().toBlob(function (blob) {
                    let formData = new FormData()

                    // Add name for our image
                    formData.append("name", name)

                    // Append image file
                    formData.append("photo", blob)
                    link.axios
                        .post(`http://localhost:8000/api/partners/${link.$route.params.id}`, formData)
                        .then((res) => {
                            link.$router.push({ name: 'home' });
                        });
                })
            },
            setImage(e) {
                const file = e.target.files[0];
                if (file.type.indexOf('image/') === -1) {
                    alert('Please select an image file');
                    return;
                }
                if (typeof FileReader === 'function') {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                      this.imgSrc = event.target.result;
                      // rebuild cropperjs with the updated source
                      this.$refs.cropper.replace(event.target.result);
                    };
                    reader.readAsDataURL(file);
                } else {
                    alert('Sorry, FileReader API not supported');
                }
            },
            showFileChooser() {
              this.$refs.input.click();
            },
        },
        components: { VueCropper}
    }
</script>
<style>
    input[type="file"] {
      display: none;
    }
    .content {
      display: flex;
      justify-content: space-between;
    }
    .cropper-area {
      width: 614px;
    }
    .actions {
      margin-top: 1rem;
    }
    .actions a {
      display: inline-block;
      padding: 5px 15px;
      background: #0062CC;
      color: white;
      text-decoration: none;
      border-radius: 3px;
      margin-right: 1rem;
      margin-bottom: 1rem;
    }
    textarea {
      width: 100%;
      height: 100px;
    }
    .preview-area {
      width: 307px;
    }
    .preview-area p {
      font-size: 1.25rem;
      margin: 0;
      margin-bottom: 1rem;
    }
    .preview-area p:last-of-type {
      margin-top: 1rem;
    }
    .preview {
      width: 100%;
      height: calc(372px * (9 / 16));
      overflow: hidden;
    }
    .crop-placeholder {
      width: 250px;
      height: 250px;
      background: #ccc;
    }
    .cropped-image img {
      width: -moz-available;
    }
</style>