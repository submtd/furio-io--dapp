import { createRouter, createWebHistory } from "vue-router";

import AddressBook from "../views/AddressBook.vue";
import Claim from "../views/Claim.vue";
import Connect from "../views/Connect.vue";
import Team from "../views/Team.vue";
import Profile from "../views/Profile.vue";
import Promo from "../views/Promo.vue";
import Swap from "../views/Swap.vue";
import Vault from "../views/Vault.vue";
import Participant from "../views/Participant.vue";
import Downline from "../views/Downline.vue";
import FurbPresale from "../views/FurbPresale.vue";
import Vote from "../views/Vote.vue";
import LPStaking from "../views/LPStaking.vue";
import NotFound from "../views/NotFound.vue";
import BuyCrypto from "../views/BuyCrypto.vue";
import PoolApr from "../views/PoolApr.vue";

const routes = [

    {
        path: "/claim",
        name: "Claim",
        component: Claim,
    },
    {
        path: "/swap",
        name: "Swap",
        component: Swap,
    },
    {
        path: "/vault",
        name: "Vault",
        component: Vault,
    },
    {
        path: "/team/:teamaddress",
        name: "Team",
        component: Team,
    },
    {
        path: "/participant/:address",
        name: "Participant",
        component: Participant,
    },
    {
        path: "/downline",
        name: "Downline",
        component: Downline,
    },
    {
        path: "/furbpresale",
        name: "FurbPresale",
        component: FurbPresale,
    },
    {
        path: "/profile",
        name: "Profile",
        component: Profile,
    },
    {
        path: "/promo",
        name: "Promo",
        component: Promo,
    },
    {
        path: "/addressbook",
        name: "AddressBook",
        component: AddressBook,
    },
    {
        path: "/updatepoolapr",
        name: "PoolApr",
        component: PoolApr,
    },
    {
        path: "/",
        name: "Home",
        component: Swap,
    },
    {
        path: "/connect",
        name: "Connect",
        component: Swap,
    },
    {
        path: "/vote",
        name: "Vote",
        component: Vote,
    },
    {
        path: "/staking",
        name: "LPStaking",
        component: LPStaking,
    },
    {
        path: "/buycrypto",
        name: "BuyCrypto",
        component: BuyCrypto,
    },
    {
        path: "/:catchAll(.*)",
        beforeEnter (to) {
            window.location.replace("/404");
        },
        component: NotFound,
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
