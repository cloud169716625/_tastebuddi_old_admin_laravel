import Vue from "vue";

import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import locale from 'element-ui/lib/locale/lang/en'
Vue.use(ElementUI, { locale });

import LocationCreate from '../components/LocationCreate';
import LocationInfo from '../components/LocationInfo';
import LocationList from '../components/LocationList';

import * as VueGoogleMaps from "vue2-google-maps";



Vue.use(VueGoogleMaps, {
    load: {
        key: process.env.GOOGLE_PLACES_API_KEY,
        libraries: "places" // necessary for places input
    }
});

window.cities = new Vue({
    el: "#content",
    components: {
        LocationCreate,
        LocationInfo,
        LocationList
    }
});
