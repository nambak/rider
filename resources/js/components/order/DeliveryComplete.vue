<template>
    <div>
        <v-easy-camera
            ref="camera"
            v-if="isShow"
            v-model="picture"
            v-on:close="close"
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
                        <b-button variant="outline-primary" @click="reset">사진 다시 찍기</b-button>
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
                    text: (typeof e.response.data === 'object') ? e.message : e.response.data,
                });
            }

            this.busy = false;
        },

        reset() {
            this.isShow = true;
            this.picture = null;
        }
    },

    watch: {
        picture() {
            if (this.picture) {
                this.close();
            }
        }
    },
}
</script>
