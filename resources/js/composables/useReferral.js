import { useStore } from "vuex";
import Cookies from "js-cookies";
import QueryString from "query-string";

export default () => {
    const store = useStore();
    const qs = QueryString.parse(location.search);
    let referrer = qs.ref;
    if(typeof referrer == "undefined") {
        referrer = Cookies.getItem("referrer");
    }
    if(referrer) {
        Cookies.setItem("referrer", referrer);
        store.commit("referrer", referrer);
    }
}
