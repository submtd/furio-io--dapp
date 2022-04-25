import { createRouter, createWebHistory } from "vue-router";

import Connect from "../views/Connect.vue";
import Presale from "../views/Presale.vue";
import Promo from "../views/Promo.vue";
import NotFound from "../views/NotFound.vue";

const routes = [
    {
        path: "/",
        name: "Presale",
        component: Presale,
    },
    {
        path: "/promo",
        name: "Promo",
        component: Promo,
    },
    {
        path: "/connect",
        name: "Connect",
        component: Connect,
    },
    {
        path: "/:catchAll(.*)",
        component: NotFound,
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
