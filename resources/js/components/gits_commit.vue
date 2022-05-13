<template>
          <div class="form-group has-feedback col-md-12 row" >
            
          <div class="form-group col-md-12">
              <input @click="commit(commit_text)" value="direct commit" name="commands"  type="button" class="btn btn-primary m-2">
              <input @click="commit(stash_text)" value="stash" name="commands"  type="button" class="btn btn-primary m-2">
              <input @click="artisan(migrate_fresh)" value="Migrate Fresh" name="commands"  type="button" class="btn btn-primary m-2">
              <input @click="artisan(rollback)" value="db rollback" name="commands"  type="button" class="btn btn-primary m-2">
              <input @click="artisan(cache)" value="Clear Cache" name="commands"  type="button" class="btn btn-primary m-2">
          </div>

            <div v-html="git_response.data"></div>

        </div>

</template>

<script>

export default {  
  data: () => ({
      commit_text:'git add . && git commit -m "browser commit"',
      stash_text:'git add . && git stash',
      rollback:'migrate:rollback',
      migrate_fresh:'migrate:fresh --seed',
      cache:'cache:clear',
      git_response : ""
  }),
  methods: {
    commit (command) { 
      axios.get('/git/git/' + command)
        .then(response => (this.git_response = response))
    },
    artisan (command) { 
      axios.get('/git/git/' + command + '/edit')
        .then(response => (this.git_response = response))
    }
  }
};
</script>

<style>

</style>
