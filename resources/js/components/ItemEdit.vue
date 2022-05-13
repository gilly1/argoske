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
            <input type="hidden" :name="`fields[${index}][id]`" :value="experience.id">
            
            <div class="form-group col-md-2">
              <div class="row">
                <div class="col-xs-2"></div>
                  <div v-if="type != 'Contract'" class="col-xs-6">
                    <button @click="addExperience" type="button" class="btn btn-sm btn-success"><i class="fa fa-plus "></i></button>
                    <button v-if="!experience.id" @click="removeField(index)" type="button" class="btn btn-sm btn-danger ml-1"><i class="fa fa-trash"></i></button>
                  </div>
              </div>
            </div> 
          </div>
              
        </div>

</template>

<script>

export default {  
  props: ['id','type'],
  data: () => ({
    stock : [],
    count : 1,
    fields: [
    ],
    db_types: [],
  }),
   mounted() {
        this.getStock();
        this.populateFields();
    },

  methods: {
    addExperience () {
      this.fields.push({
        id:'',
        name: '',
        quantity: '',
        duration: '',
        installment: ''
      })
    },
    removeField (index) {
      this.fields.splice(index, 1);
    },
    onSelectModelMapping(event){
      this.fields = [
          {
            id:"",
            name: "",
            quantity: '',
            duration: '',
            installment: ''
          }
        ];
        this.count = 1;
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
    populateFields(){
      if(this.type == 'Contract'){
        var url = '/api/populateContractFields/' + this.id; 
      }else{
        var url = '/api/populateFields/' + this.id; 
      }
        axios.get(url)
            .then((response) => {
                response.data.forEach(element => { 
                  this.fields.push({
                    id:element.id,
                    name: element.stock_id,
                    quantity: element.quantity,
                    duration: element.duration,
                    installment: element.installments
                  })
                });

            })
            .catch( resonse => {
                console.log(resonse);
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
