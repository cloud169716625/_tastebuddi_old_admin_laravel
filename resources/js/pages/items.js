import Vue from "vue";

import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import locale from "element-ui/lib/locale/lang/en";
Vue.use(ElementUI, { locale });

import ItemCreate from '../components/ItemCreate';
import ItemInfo from '../components/ItemInfo';
import ItemList from '../components/ItemList';

window.cities = new Vue({
    el: "#content",
    components: {
        ItemCreate,
        ItemInfo,
        ItemList
    }
});
