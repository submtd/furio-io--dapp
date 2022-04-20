import { useStore } from "vuex";
import useAlerts from "./useAlerts";
export default () => {
    const store = useStore();
    const alerts = useAlerts();
    const update = async () => {
        await axios.get("/api/v1/settings").then(response => {
            const settings = store.state.settings;
            for(const property in response.data) {
                settings[property] = response.data[property];
            }
            store.commit("settings", settings);
        }).catch(error => {
            alerts.danger(error.message);
        });
    }
    return {
        update,
    }
}
