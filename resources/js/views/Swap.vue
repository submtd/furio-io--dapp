<template>
    <h1>Swap</h1>
    <div class="bg-light text-dark rounded p-5">
        <div class="row">
            <div class="col-md-5 mb-2">
                <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Explicabo similique hic corporis laudantium provident numquam culpa autem libero maxime veritatis, nulla dignissimos necessitatibus itaque dicta beatae quos repellat asperiores quas.</p>
            </div>
            <div class="col-md-7 mb-2">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a @click="activateSwap" class="nav-link" :class="swapActive" href="#">Swap</a>
                    </li>
                    <li class="nav-item">
                        <a @click="activateBuy" class="nav-link" :class="buyActive" href="#">Buy</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
import { ref } from "vue";

export default {
    setup () {
        const swapActive = ref("active");
        const buyActive = ref("");

        const activateSwap = () => {
            swapActive.value = "active";
            buyActive.value = "";
        }

        const activateBuy = () => {
            buyActive.value = "active";
            swapActive.value = "";
        }

        return {
            swapActive,
            buyActive,
            activateSwap,
            activateBuy,
        }
    }
}
</script>
