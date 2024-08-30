<template>
    <div>
        <h1 ref="myref"> Hook tutorial</h1>
        <h2>My name is : {{ name }}</h2>
    </div>
</template>

<script>
import { thru } from 'lodash';
import { create } from 'lodash';

    export default{
        data(){
            return{
                name: "sadek"
            }
        },
        beforeCreate(){
            console.log(this.name)
        },
        created(){
            // console.log(this.$refs.myref)
            console.log('data and other function is avilable but not html',this.name)
        },
        munted(){
            console.log(this.$refs)
        }
    }
</script>   