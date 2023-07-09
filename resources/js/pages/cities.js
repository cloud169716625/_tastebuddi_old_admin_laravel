import Vue from "vue";

import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
Vue.use(ElementUI);

import CityList from '../components/CityList';
import CityCreate from '../components/CityCreate';
import CityInfo from '../components/CityInfo';

window.cities = new Vue({
    el: "#content",
    components: {
        CityList,
        CityCreate,
        CityInfo
    }
});
