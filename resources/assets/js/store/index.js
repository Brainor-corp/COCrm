import Vue from 'vue';
import Vuex from 'vuex';
import title from './modules/title';

Vue.use(Vuex);


export default new Vuex.Store({
    modules: {
        title
}
});