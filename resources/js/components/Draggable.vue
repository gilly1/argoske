<template>
  <div class="row">

    <div class="col-md-12">
      <div for="type" class="form-group">
        <label>Select Template</label>
        <select  v-model="selectedTemplate" @change="selectedTemplateName($event)" class="form-control select2" style="width: 100%;" id="type" name="file_type">
        <option v-for="option in templates" v-bind:value="option.columns">
          {{ option.name }}
        </option>
        </select>
      </div>

    </div>

    <div class="col-md-6">      
      <div class="card card-row card-secondary">
        <div class="card-header">
            <h3 class="card-title">
              Table Columns
            </h3>
          </div>        
                <draggable
                  class="list-group draggable_fill_area"
                  :list="tableColumns"
                  group="people"
                  @change="log"
                  itemKey="name"
                > 
                <template  #item="{ element, index }">
                      <button type="button" class="btn btn-info btn-sm btn-block">{{ element.name }}</button>
                </template>
                </draggable>
        </div>
    </div>
    <div class="col-md-6">      
      <div class="card card-row card-secondary">
        <div class="card-header">
            <h3 class="card-title">
              Drop here & rearrange
            </h3>
            <button type="button" class="btn btn-primary btn-sm ml-2" data-toggle="modal" data-target="#save_template">
                  Save Template
                </button>
          </div>        
                <draggable
                  class="list-group draggable_fill_area"
                  :list="selectedTableColumns"
                  group="people"
                  @change="pushToSend"
                  itemKey="name"
                > 
                <template  #item="{ element, index }">
                      <button type="button" class="btn btn-success btn-sm btn-block">{{ element.name }}</button>
                </template>
                </draggable>
        </div>
    </div>


    
      <div class="modal fade" id="save_template">
        <div class="modal-dialog save_template">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Template Name</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <input type="text" class="form-control" id="template_name" name="template_name" v-model="template_name" placeholder="Template name">
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" @click="add">Save</button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>

    <input type="hidden" name="columns" required :value="sendColumns">
    <input type="hidden" name="table" required :value="table">



    <rawDisplayer :value="tableColumns" title="All Columns" />

    <rawDisplayer  :value="selectedTableColumns" title="Selected Columns" />

    <div class="col-md-6">
      <div class="form-group">
        <label for="file_name">Save As</label>
        <input type="text" class="form-control" id="file_name" name="file_name" placeholder="Enter email">
      </div>
    </div>
    <div class="col-md-6">
      <div for="type" class="form-group">
        <label>Format</label>
        <select class="form-control select2" style="width: 100%;" id="type" name="file_type">
          <option value="xlsx">XLSX</option>
          <option value="csv">CSV</option>
          <option value="pdf">PDF</option>
        </select>
      </div>
    </div>


  </div>
</template>
<script>
import draggable from "vuedraggable";
export default {
  name: "two-lists",
  display: "Two Lists",
  order: 1,
  components: {
    draggable
  },
  data() {
    return {
        tableColumns: [],
        selectedTableColumns: [],
        sendColumns:[],
        table:'',
        template_name:'',
        selectedTemplate:'',
        templates:[]
    };
  },
   mounted() {
        this.table = new URL(location.href).searchParams.get('name');
        this.getTableColumns(this.table);
        this.getTemplates(this.table);
    },
  methods: {
    pushToSend: function() {
        //get name value from selectedTableColumns
        //then assign it to sendColumnsl

        var col = [];
        for( var item in this.selectedTableColumns)
        {
            col.push(this.selectedTableColumns[item]['name'])
        }
        this.sendColumns = col;
    },
    add: function(){
      var data = {
        name : this.template_name,
        table : this.table,
        columns : this.sendColumns
      }; 

      axios.post('/api/reports/saveTemplate', data).then(function (response) {
          $('#save_template').modal('hide');
      });
    },
    getTableColumns(table){
        axios.get('/api/reports/' + table)
            .then((response) => {
                this.tableColumns = response.data;

            })
            .catch( resonse => {
                console.log('error');
            })
    },
    getTemplates(table){
        axios.get('/api/reports/templates/' + table)
            .then((response) => {
                this.templates = response.data;

            })
            .catch( resonse => {
                console.log('error');
            })
    },
    selectedTemplateName(event){
      var list = [];
      var savedColumnsArray = Object.values(event.target.value.split(','));
      for(var i in  savedColumnsArray){
        var savedColumns = new Object();
        savedColumns['name'] = savedColumnsArray[i];
        list.push(savedColumns);
      } 

      this.selectedTableColumns = list;
      this.pushToSend();
    }
  }
};
</script>

<style scoped>
.draggable_fill_area{
  min-height: 40vh;
  
}
</style>
