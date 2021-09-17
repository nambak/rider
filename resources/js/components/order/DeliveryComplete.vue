<template>
    <div>
        <v-easy-camera v-if="isShow" v-model="picture" ref="camera" v-on:close="close"></v-easy-camera>
        <div v-if="picture">
            <b-row>
                <b-col class="text-center">
                    <b-img :src="this.picture" fluid></b-img>
                </b-col>
            </b-row>
            <b-row>
                <b-col class="text-center mt-3">
                    <b-button variant="primary" @click="completedDelivery">사진올리고 배송완료 하기</b-button>
                </b-col>
            </b-row>
        </div>
    </div>
</template>

<script>
import EasyCamera from 'easy-vue-camera';

export default {
    name: 'DeliveryComplete',

    components: {
        'v-easy-camera': EasyCamera,
    },

    data() {
        return {
            picture: null,
            isShow: true,
        }
    },

    methods: {
        close() {
            this.isShow = false;
        },

        completedDelivery() {
            this.$swal({
                icon: 'success',
                title: '임무 완료'
            });
        }
    },

    watch: {
        picture() {
            if (this.picture) {
                this.close();
            }
        }
    }
}
</script>

<style scoped>

</style>
