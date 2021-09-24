<template>
    <div>
        <v-easy-camera
            ref="camera"
            v-if="isShow"
            v-model="picture"
            v-on:close="close"
            startOnMounted
        ></v-easy-camera>
        <div v-if="picture">
            <b-row>
                <b-col class="text-center">
                    <b-img :src="this.picture" fluid></b-img>
                </b-col>
            </b-row>
            <b-row>
                <b-col class="text-center mt-3">
                    <b-overlay
                        :show="busy"
                        rounded
                        opacity="0.6"
                        spinner-small
                        spinner-variant="primary"
                        class="d-inline-block"
                        @hidden="onHidden"
                    >
                        <b-button variant="primary" ref="button" :disabled="busy" @click="completedDelivery">
                            사진올리고 배송완료 하기
                        </b-button>
                    </b-overlay>
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

    props: ['orderId'],

    data() {
        return {
            picture: null,
            isShow: true,
            busy: false,
        }
    },

    methods: {
        onHidden() {
            this.$refs.button.focus();
        },

        close() {
            this.isShow = false;
        },

        async completedDelivery() {
            this.busy = true;

            try {
                let formData = new FormData;

                formData.append('image', this.picture);

                const response = await axios.post(`/api/order/${this.orderId}/completed`, formData);

                if (response.status === 200) {
                    await this.$swal({
                        icon: 'success',
                        title: '임무 완료'
                    });

                    location.href = '/home'
                }
            } catch (e) {
                this.$swal({
                    icon: 'error',
                    title: '오류',
                    text: e.message,
                });
            }

            this.busy = false;
        },
    },

    watch: {
        picture() {
            if (this.picture) {
                this.close();
            }
        }
    },

    mounted() {
        console.log('dom render completed');
        // switchCamera(isLoading) {
        //     if (!isLoading) {
        //         this.$refs.camera.switchCamera(true);
        //         console.log('camera is loading');
        //     } else {
        //         console.log('camera not loading');
        //     }
        // },
    }
}
</script>
