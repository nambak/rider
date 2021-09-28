<template>
    <div>
        <h1 class="text-center">상품 QR코드 스캔</h1>
        <qrcode-stream
            class="qrcode"
            @init="onInit"
            @decode="onDecode"
            v-if="!destroyed"
            :camera="camera"
            :track="paintOutline"
        >
            <div v-show="showScanConfirmation" class="scan-confirmation">
                <img src="/images/checkmark.svg" alt="CheckMark" width="128px">
            </div>
            <div class="loading-indicator" v-if="loading">
                Loading...
            </div>
        </qrcode-stream>
        <div class="text-center mt-2">
            <b-button @click="reload" variant="outline-primary">QR 스캐너 새로고침</b-button>
        </div>
        <b-card header="주문내역" class="mt-3">
            <b-card-text>
                <ul>
                    <li v-for="item in items" v-bind:key="item.id">
                        <span v-bind:class="{ pickup: item.picked === item.quantity }">
                            {{ item.product_name }} ({{ item.picked }}/{{ item.quantity }})
                        </span>
                    </li>
                </ul>
                <b-row>
                    <b-col class="text-center">
                        <b-button variant="primary" @click="completedPickUp">
                            상품 픽업 완료
                        </b-button>
                    </b-col>
                </b-row>
            </b-card-text>
        </b-card>
    </div>
</template>

<script>
export default {
    name: 'PickUp',

    props: ['orderId'],

    mounted() {
        this.getOrderDetails();
    },

    data() {
        return {
            items: [],
            camera: 'auto',
            showScanConfirmation: false,
            isNotCompletedPickUp: true,
            loading: false,
            destroyed: false,
        }
    },

    methods: {
        async onInit (promise) {
            this.loading = true;

            try {
                await promise;
            } catch (error) {
                console.error(error);
            } finally {
                this.showScanConfirmation = this.camera === 'off';
                this.loading = false;
            }
        },

        async reload() {
            this.destroyed = true;

            await this.$nextTick();

            this.destroyed = false;
        },

        async onDecode(decodedString) {
            if (this.isNotCompletedPickUp) {
                this.pickUpGoods(decodedString);
                this.pause();
                await this.timeout(1000);
                this.unpause();
            }
        },

        unpause() {
            this.camera = 'auto';
        },

        pause() {
            this.camera = 'off';
        },

        timeout(ms) {
            return new Promise(resolve => {
                window.setTimeout(resolve, ms);
            });
        },

        paintOutline (detectedCodes, ctx) {
            for (const detectedCode of detectedCodes) {
                const [ firstPoint, ...otherPoints ] = detectedCode.cornerPoints

                ctx.strokeStyle = "red";

                ctx.beginPath();
                ctx.moveTo(firstPoint.x, firstPoint.y);
                for (const { x, y } of otherPoints) {
                    ctx.lineTo(x, y);
                }
                ctx.lineTo(firstPoint.x, firstPoint.y);
                ctx.closePath();
                ctx.stroke();
            }
        },

        pickUpGoods(goodsCode) {
            let index = this.items.findIndex(item => item.product_code === goodsCode)

            if (index !== -1) {
                if (this.items[index].picked !== this.items[index].quantity) {
                    this.items[index].picked += 1;
                } else {
                    this.$swal({
                        icon: 'error',
                        title: '오류',
                        text: '주문 수량 초과'
                    })
                }
            } else {
                this.$swal({
                    icon: 'error',
                    title: '오류',
                    text: '주문 내역에 없는 상품'
                });

                return false;
            }

            this.checkCompletePickUp();
        },

        completedPickUp() {
            // TODO: update order pick up completed time
            location.href = `/my_orders/${this.orderId}`
        },

        async getOrderDetails() {
            try {
                const response = await axios.get(`/api/order/${this.orderId}/details`);

                this.items = response.data.data;
            } catch (e) {
                this.$swal({
                    icon: 'error',
                    title: '오류',
                    text: e.message,
                });
            }
        },

        checkCompletePickUp() {
            let filter = this.items.filter(item => item.quantity === item.picked);

            if (filter.length === this.items.length) {
                this.isNotCompletedPickUp = false;
            }
        }
    },
}
</script>

<style scoped>
    .pickup {
        text-decoration: line-through;
    }

    .scan-confirmation {
        position: absolute;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, .8);
        display: flex;
        flex-flow: row nowrap;
        justify-content: center;
    }

    .loading-indicator {
        font-weight: bold;
        font-size: 2rem;
        text-align: center;
    }

    .qrcode {
        height: 250px;
    }
</style>

