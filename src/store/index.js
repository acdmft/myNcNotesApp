import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const store = new Vuex.Store({
  state: {
    currentNote: {
      active: false,
      title: "",
      content: "",
    }
  }, 
  mutations: {
    
  }
})
export default store
