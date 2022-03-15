<template>
    <div>
        <b-card :header="order.state" :header-bg-variant="stateColor" header-text-variant="white" class="mb-5">
            <b-card-text>
                <p>주문번호: {{ data.order_id || data.order_number }}</p>
                <p>주문일: {{ data.order_date }}</p>
                <p>고객명 : {{ data.buyer_name }}</p>
                <p>고객연락처: {{ data.buyer_cellphone }}</p>
                <p>주소: {{ data.receiver_address_full }}</p>
                <p>메세지: {{ data.shipping_message }}</p>

                <b-row>
                    <b-col class="text-center">
                        <b-overlay
                            :show="isLoading"
                            rounded
                            opacity="0.6"
                            spinner-small
                            spinner-variant="primary"
                            class="d-inline-block"
                            @hidden="onHidden"
                        >
                            <b-button
                                v-if="order.state ==='배송전' || order.state === '배송준비'"
                                variant="primary"
                                @click="startDelivery"
                                ref="actionButton"
                                :disabled="isLoading"
                                block
                                size="lg"
                            >배송시작</b-button>
                            <b-button
                                v-else-if="order.state === '배송중'"
                                variant="primary"
                                @click="completeDelivery"
                                ref="actionButton"
                                :disabled="isLoading"
                                block
                                size="lg"
                            >배송완료</b-button>
                        </b-overlay>
                    </b-col>
                </b-row>
            </b-card-text>
        </b-card>
        <b-row>
            <b-col class="text-center">
                <b-button variant="warning">
                    <a :href="kakaoMapLink" target="_blank">카카오 맵 경로찾기</a>
                </b-button>
            </b-col>
        </b-row>

    </div>
</template>

<script>
export default {
    name: 'MyOrders',

    props: ['order'],

    data() {
        return {
            data: [],
            kakaoMapLink: '',
            actionName: '배송시작',
            isLoading: false,
        }
    },

    mounted() {
        this.getOrder();
    },

    methods: {
        onHidden() {
            this.$refs.actionButton.focus();
        },

        completeDelivery() {
            location.href = `/order/${this.order.id}/delivery_complete`;
        },

        async startDelivery() {
            this.isLoading = true;

            try {
                if (this.order_id) {
                    await axios.post(`https://deliver.10tenminute.xyz/api/persist_shipment`, {
                        'order_number': this.order.order_id,
                    });
                }

                await axios.post(`/api/order/${this.order.id}/start_delivery`);
                location.reload();
            } catch (e) {
                if (e.response.status === 422) {
                    this.$swal({
                        icon: 'error',
                        text: '이미 처리된 주문입니다. 페이지를 새로고침 해주세요.'
                    });
                } else {
                    this.$swal({
                        icon: 'error',
                        title: '오류',
                        text: e.message,
                    });
                }
            } finally {
                this.isLoading = false;
            }
        },

        async getGeoLocation() {
            let geocoder = new kakao.maps.services.Geocoder();
            geocoder.addressSearch(this.data.delivery.address1, (result, status)  => {
                if (status === kakao.maps.services.Status.OK) {
                    this.kakaoMapLink =
                        'https://map.kakao.com/link/to/'
                        + result[0].address_name + ','
                        + result[0].y + ','
                        + result[0].x;
                }
            });
        },

        async getOrder() {
            try {
                const response = await axios.get(`/api/order/${this.order.order_id}`);

                if (response.status === 200) {
                    this.data = response.data;
                    this.getGeoLocation();
                }

                if (this.data.state === '배송완료') {
                    this.$swal({
                        icon: 'error',
                        title: '오류',
                        text: '이미 배송완료된 주문입니다.',
                        confirmButtonText: '닫기'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.href = '/home';
                        }
                    });
                }
            } catch (e) {
                this.$swal({
                    icon: 'error',
                    title: '오류',
                    text: e.message
                });
            }
        }
    },

    computed: {
        stateColor() {
            switch (this.data.state) {
                case '팍업완료':
                    return 'success';
                case '배송중':
                    return 'primary';
                default:
                    return 'success';
            }
        },
    }
}
</script>
