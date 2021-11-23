import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);

const state = {
  searchedVideos: [],
  videos:[
    
  ]
};

const paginated = (items, n) => new Array(Math.ceil(items.length / n))
    .fill()
    .map(() => items.splice(0, n));

const searchFunc = (state, author, search) => search == null ?
      state.videos
    :
    name == null?
        state.videos:
        state.videos.filter(
            v =>
                v.name.includes(search));

const getters={
  paginatedVideos: (state) => (author, search, count) => paginated(searchFunc(state,author, search), count),
  allVideos: (state) => (author, search) => searchFunc(state, author, search),
  getVideoById: (state) => (id) => state.videos.find(v =>
      v.status === "AVAILABLE" &&
      v.id === parseInt(id) ),
  allVideosByTag:(state) => (tag) => state.videos.find(v =>
      v.status === "AVAILABLE" &&
      v.tags.includes(tag))
};

const actions = {
  setInitialData(state, payload) {
    this.commit('setInitialData', payload)
  },
};

const mutations = {
  setInitialData(state, payload) {
      let data = payload.products
      console.log(data)
      state.videos = data
  },
};

export default new Vuex.Store({
  state,
  mutations,
  actions,
  getters
});