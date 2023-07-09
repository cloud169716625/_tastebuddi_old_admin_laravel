import Vue from "vue";

import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import locale from "element-ui/lib/locale/lang/en";
Vue.use(ElementUI, { locale });

import CountryList from "../components/CountryList";
import CountryInfo from "../components/CountryInfo";

window.countries = new Vue({
    el: "#content",
    components: {
        CountryInfo,
        CountryList
    }
});
