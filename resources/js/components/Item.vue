<template>
        <div>

          <div style="width:65vw" class="form-group has-feedback col-md-12 row"  v-for="(experience, index) in fields" :key="index">

            <div class="form-group col-md-3">
              <select v-model="experience.name" :name="`fields[${index}][name]`" type="text" class="form-control" >
                  <option v-for="appr in stock" :key="appr.stock_id" :value="appr.stock_id">{{appr.description}}</option>
              </select>
            </div>
            <div class="form-group col-md-2">
              <input v-model="experience.quantity"  :name="`fields[${index}][quantity]`" type="text" class="form-control" placeholder="quantity">
            </div>
            <div class="form-group col-md-2">
              <input v-model="experience.duration"  :name="`fields[${index}][duration]`" type="text" class="form-control" placeholder="duration">
            </div>
            <div class="form-group col-md-2">
              <input v-model="experience.installment"  :name="`fields[${index}][installment]`" type="text" class="form-control" placeholder="installment">
            </div>
            
            <div class="form-group col-md-2">
              <div class="row">
                <div class="col-xs-2"></div>
                  <div class="col-xs-6">
                    <button @click="addExperience" type="button" class="btn btn-sm btn-success"><i class="fa fa-plus "></i></button>
                    <button @click="removeField(index)" type="button" class="btn btn-sm btn-danger ml-1"><i class="fa fa-trash"></i></button>
                  </div>
              </div>
            </div> 
          </div>
              
        </div>

</template>

<script>


export default {  
  data: () => ({
    stock : [],
    count : '',
    fields: [
      {
        name: "",
        quantity: '',
        duration: '',
        installment: ''
      }
    ],
    db_types: [],
  }),
   mounted() {
        this.getStock();
    },

  methods: {
    addExperience () {
      this.fields.push({
        name: '',
        duration: this.count,
        quantity: '',
        installment:this.count
      })
    },
    removeField (index) {
      this.fields.splice(index, 1);
    },
    onSelectModelMapping(event){
      this.fields = [
          {
            name: "",
            quantity: '',
            duration: '',
            installment: ''
          }
        ];
        this.count = '';
      this.getStock(event.target.value);
    },
    getStock(){
        axios.get('/api/getStock/')
            .then((response) => {
                this.stock = response.data;

            })
            .catch( resonse => {
                console.log('error');
            })
    },

    submit () {
      const data = {
        fields: this.fields
      }
      alert(JSON.stringify(data, null, 2))
    }
  }
};
</script>

<style>

</style>
