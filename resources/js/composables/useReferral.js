import { useStore } from "vuex";
import QueryString from "query-string";
import useCookies from "./useCookies";

export default () => {
    const store = useStore();
    const cookies = useCookies();
    const qs = QueryString.parse(location.search);
    let referrer = qs.ref;
    if(typeof referrer == "undefined") {
        referrer = cookies.get("referrer");
    }
    if(referrer) {
        cookies.set("referrer", referrer);
        store.commit("referrer", referrer);
    }
}
