import Vue from "vue";

import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import locale from "element-ui/lib/locale/lang/en";
Vue.use(ElementUI, {locale});

import CategoryList from "../components/CategoryList";

window.categories = new Vue({
    el: "#content",
    components: {
        CategoryList
    }
});
