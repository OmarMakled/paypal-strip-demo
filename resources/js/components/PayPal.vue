<template>
  <div ref="paypal"></div>
</template>

<script>
export default {
  methods: {
    init() {
      const script = document.createElement("script");
      script.src = `https://www.paypalobjects.com/api/checkout.js`;
      script.addEventListener("load", this.setLoaded);
      document.body.appendChild(script);
    },
    setLoaded() {
      paypal.Button.render(
        {
          env: window.app.paypal.env,
          style: {
            size: "large",
            color: "gold",
            shape: "pill"
          },
          payment: function(data, actions) {
            return actions.request
              .post("/api/payments/create")
              .then(function(res) {
                return res.id;
              });
          },
          onAuthorize: function(data, actions) {
            return actions.request
              .post("/api/payments/execute", {
                paymentID: data.paymentID,
                payerID: data.payerID
              })
              .then(function(res) {
                console.log(res);
                alert("PAYMENT WENT THROUGH!!");
              });
          }
        },
        this.$refs.paypal
      );
    }
  },
  mounted() {
    this.init();
  }
};
</script>

<style>
</style>