<template>
    <div>
        <h2 class="text-center">Partners List</h2>
        
        <div class="container bg-light">
        <div class="row mt-5 justify-content-center">
          <div class="col-sm-8 col-md-8 col-lg-6 m-lg-auto">
            <div class="input-group">
              <input
                type="search"
                class="form-control rounded"
                placeholder="Type here"
                aria-label="Search"
                aria-describedby="search-addon"
                v-model="searchString"
              />
              <button type="button" class="btn btn-outline-danger" @click="search">
                Search Partners
              </button>
            </div>
          </div>
        </div>

        <div class="row mt-5 justify-content-end">
          <div class="col-2">
            <select
              class="form-select form-select-sm"
              aria-label=".form-select-lg example"
              @change="selectPerPageCount"
            >
              <option selected>Per page</option>
              <option v-for="i in 5" :key="i * 5" :value="i * 5">
                {{ i * 5 }}
              </option>
            </select>
          </div>
        </div>

        <table class="table">
            <thead>
            <tr>
                <th width="10%">ID</th>
                <th width="30%">Name</th>
                <th width="30%">Photo</th>
                <th width="30%">Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="product in this.videosToShow" :key="product.id">
                <td>{{ product.id }}</td>
                <td>{{ product.name }}</td>
                <td><img :src="getLogo(product.photo)" class = "logo" /></td>
                <td>
                    <div class="btn-group" role="group">
                        <router-link :to="{name: 'edit', params: { id: product.id }}" class="btn btn-success">Edit</router-link>
                        <button class="btn btn-danger" @click="deleteProduct(product.id)">Delete</button>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>

        <div class="row justify-content-end">
          <div class="col-12 text-center">
            <nav aria-label="...">
              <ul class="pagination justify-content-end pagination-sm">
                <li class="page-item" v-bind:class="{ disabled: previousButton }">
                  <a
                    href="#"
                    class="page-link"
                    @click="
                      selectedPage + 1 > 1 ? (selectedPage -= 1) : selectedPage
                    "
                    >Previous</a
                  >
                </li>

                <li
                  class="page-item"
                  v-for="index in this.totalPageCount"
                  :key="index"
                  :value="index"
                >
                  <a class="page-link" href="#" @click="selectPage">{{ index }}</a>
                </li>

                <li class="page-item" v-bind:class="{ disabled: nextButton }">
                  <a
                    class="page-link"
                    href="#"
                    @click="
                      selectedPage + 1 === totalPageCount
                        ? selectedPage
                        : (selectedPage += 1)
                    "
                    >Next</a
                  >
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>
</template>
 
<script>
    import { mapGetters } from "vuex";
    import _ from "lodash";
    export default {
        data() {
            return {
                searchString: null,
                oldSearchString: null,
                searchedVideos: [],
                selectedPage: 0,
                videosPerPage: 0,
                videoChunks: []
            }
        },
        created() {
            this.axios
                .get('http://localhost:8000/api/partners')
                .then(response => {
                    let products = response.data;
                    this.$store.dispatch('setInitialData', {
                        products: products,
                    })
                });
            this.videosPerPage = 10;
            this.selectedPage = 0;
            this.searchedVideos = this.allVideos(null, null);
            this.videoChunks = _.chunk(this.searchedVideos, this.videosPerPage);
            this.search();
        },
        methods: {
            getLogo(logo){
                return logo;
            },
            deleteProduct(id) { 
                this.axios
                    .delete(`http://localhost:8000/api/partners/${id}`)
                    .then(response => {
                        let i = this.videosToShow.map(data => data.id).indexOf(id);
                        this.videosToShow.splice(i, 1)
                    });
            },
            
            search() {
                this.videosPerPage = 10;
                this.selectedPage = 0;
                // if (
                //     this.searchString !== "" &&
                //     this.oldSearchString !== this.searchString
                // ) {
                    this.searchedVideos = this.allVideos(null, this.searchString);
                    this.videoChunks = _.chunk(this.searchedVideos, this.videosPerPage);
                // }
                this.oldSearchString = this.searchString;
                // this.searchedVideos = this.allVideos(null, this.oldSearchString);
            },
            selectPerPageCount(event) {
                this.selectedPage = 0;
                this.videosPerPage = event.target.value;
                this.videoChunks = _.chunk(this.searchedVideos, this.videosPerPage);
            },
            selectPage(event) {
                this.selectedPage = event.target.text - 1;
            },
        },
        computed: {
            ...mapGetters(["allVideos"]),
            videosToShow() {
              return this.videoChunks[this.selectedPage];
            },
            totalPageCount() {
              return this.videoChunks.length;
            },
            previousButton() {
              return !(this.totalPageCount === 1 || this.selectedPage !== 0);
            },
            nextButton() {
              return !(this.selectedPage + 1 < this.totalPageCount);
            },
        }
    }
</script>