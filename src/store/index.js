import Vue from 'vue'
import Vuex, {Store} from 'vuex'

Vue.use(Vuex)

const store = new Store({
  state: {

    currentNote: {
      id: null,
      active: false,
      title: "",
      content: "",
    },
  },
  mutations: {
    
  }
})
export default store
