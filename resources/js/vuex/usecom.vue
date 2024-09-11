<template>
    <div>
        <div class="content">
            <div class="container-fluid">
                <h1>I will show how all other components react to changes</h1>
                <h2>
                    Current state of counter is : {{ $store.state.counter }}
                </h2>
                <div>
                    <comA></comA>
                </div>
                <div>
                    <comB></comB>
                </div>
                <div>
                    <comC></comC>
                </div>

                <Button type="info" @click="changeCounter">Change the state of the counter</Button>
            </div>
        </div>
    </div>
</template>

<script>
import comA from "./comA.vue";
import comC from "./comC.vue";
import comB from "./comB.vue";
import { mapGetters,mapActions } from "vuex";
import { method } from "lodash";
export default {
    data() {
        return {
            localVar:'some value'
        };
    },
    method:{
        ...mapActions([
            'changeCounterAction'
        ])
    },
    computed:{
        ...mapGetters({
            'counter':'getCounter'
        })
    },
    created() {},
    components: {
        comA,
        comB,
        comC
    },
    methods:{
        changeCounter(){
            this.$store.dispatch('changeCounterAction',1)
            // this.$store.commit("changeTheCounter",1)
        },
        runSomethingWhenCounterChnage(){
            console.log('i am running based on each changes happening')
        }
    },
    watch : {
        counter(value){
            console.log('counter is changing',value)
            this.runSomethingWhenCounterChnage()
            console.log('loacl var ',this.localVar)
        }
    }
};
</script>
