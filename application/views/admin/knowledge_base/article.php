<?php init_head(); ?>
<div id="wrapper">
<div class="content">
   <?php echo form_open($this->uri->uri_string()); ?>
   <div class="row">
      <div class="col-md-12">
         <div class="panel_s">
            <div class="panel-body">
               <h4 class="no-margin">
                  <?php echo $title; ?>
                  <?php if(isset($article)){ ?>
                  <p>
                     <small>
                     <?php echo _l('article_total_views'); ?>: <?php echo total_rows('tblviewstracking',array('rel_type'=>'kb_article','rel_id'=>$article->articleid)); ?>
                     </small>
                  </p>
                  <?php } ?>
               </h4>
               <hr class="hr-panel-heading" />
               <?php if(isset($article)){ ?>
               <?php if(has_permission('knowledge_base','','create')){ ?>
               <a href="<?php echo admin_url('knowledge_base/article'); ?>" class="btn btn-success pull-left mbot20 display-block"><?php echo _l('kb_article_new_article'); ?></a>
               <?php } ?>
               <?php if(has_permission('knowledge_base','','delete')){ ?>
               <a href="<?php echo admin_url('knowledge_base/delete_article/'.$article->articleid); ?>" class="btn btn-danger _delete pull-left mbot20 mleft5 display-block"><?php echo _l('delete'); ?></a>
               <?php } ?>
<!--                   <a href="--><?php //echo (isset($article) && $article->staff_article == 1)?admin_url("knowledge_base/view/".$article->slug):base_url("clients/knowledge_base/".$article->slug); ?><!--" target="_blank" class="btn btn-info pull-right mbot20 mleft5 display-block">Visualizar</a>-->
                   <a href="<?php echo (isset($article) && $article->staff_article == 1)?admin_url("knowledge_base/view/".$article->slug):base_url("clients/knowledge_base/".$article->slug); ?>" class="pull-right" required="true">
                        <h4>
                            <span>
                                <i class="fa fa-arrow-right" aria-hidden="true"></i>
                            </span>
                        </h4>
                   </a>
               <?php } ?>
               <div class="clearfix"></div>
               <?php $value = (isset($article) ? $article->subject : ''); ?>
               <?php $attrs = (isset($article) ? array() : array('autofocus'=>true)); ?>
               <?php echo render_input('subject','kb_article_add_edit_subject',$value,'text',$attrs); ?>
               <?php if(isset($article)){
                  echo render_input('slug','<small class="req text-danger">* </small>Slug',$article->slug,'text', array("required"=>"true"));
                  } ?>
               <?php $value = (isset($article) ? $article->articlegroup : ''); ?>
               <?php echo render_select('articlegroup',get_kb_groups(),array('groupid','name'),'kb_article_add_edit_group',$value); ?>
               <div class="checkbox checkbox-primary">
                  <input type="checkbox" id="staff_article" name="staff_article" <?php if(isset($article) && $article->staff_article == 1){echo 'checked';} ?>>
                  <label for="staff_article"><?php echo _l('internal_article'); ?></label>
               </div>
               <div class="checkbox checkbox-primary">
                  <input type="checkbox" id="disabled" name="disabled" <?php if(isset($article) && $article->active_article == 0){echo 'checked';} ?>>
                  <label for="disabled"><?php echo _l('kb_article_disabled'); ?></label>
               </div>
               <p class="bold"><?php echo _l('kb_article_description'); ?></p>
               <?php $contents = ''; if(isset($article)){$contents = $article->description;} ?>
               <?php echo render_textarea('description','',$contents,array(),array(),'','tinymce'); ?>
               <?php if((has_permission('knowledge_base','','create') && !isset($article)) || has_permission('knowledge_base','','edit') && isset($article)){ ?>
               <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
               <?php } ?>
            </div>
         </div>
      </div>
      <?php echo form_close(); ?>
   </div>
</div>
    <a href="#top" id="toplink">↑</a>
    <a href="#bot" id="botlink">↓</a>
</div>
<?php init_tail(); ?>
<script>
   $(function(){
    _validate_form($('form'),{subject:'required',articlegroup:'required'});

       $("a[href='#top']").on("click", function (e) {
           e.preventDefault();
           $("html,body").animate({scrollTop: 0}, 1000);
           e.preventDefault();
       });

       // Smooth scroll to bottom.
       $("a[href='#bot']").on("click", function (e) {
           e.preventDefault();
           $("html,body").animate({scrollTop: $(document).height()}, 1000);
           e.preventDefault();
       });

   });
</script>
</body>
</html>
