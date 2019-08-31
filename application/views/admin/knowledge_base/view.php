<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body tc-content">
                       <h4 class="bold no-margin"><?php echo $article->subject; ?>
                           <?php if(has_permission('knowledge_base', '', 'edit')){ ?>
                            <a href="<?php echo admin_url("knowledge_base/article/".$article->articleid); ?>" class="pull-right">
                                <span>
                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                </span>
                            </a>
                           <?php } ?>
                       </h4>
                       <hr />
                       <div class="clearfix"></div>
                       <?php echo $article->description; ?>
                       <hr />
                       <h4 class="mtop20"><?php echo _l('clients_knowledge_base_find_useful'); ?></h4>
                       <div class="answer_response"></div>
                       <div class="btn-group mtop15 article_useful_buttons" role="group">
                        <input type="hidden" name="articleid" value="<?php echo $article->articleid; ?>">
                        <button type="button" data-answer="1" class="btn btn-success"><?php echo _l('clients_knowledge_base_find_useful_yes'); ?></button>
                        <button type="button" data-answer="0" class="btn btn-danger"><?php echo _l('clients_knowledge_base_find_useful_no'); ?></button>
                    </div>
                        <a href="<?php echo base_url("knowledge_base/export/".$article->articleid); ?>" class="btn btn-info pull-right">
                            PDF
                        </a>
                </div>
            </div>
        </div>
        <?php if(count($related_articles) > 0){ ?>
        <div class="col-md-12">
          <div class="panel_s">
              <div class="panel-body">
                  <div id="accordion">
                      <div class="card">
                          <div class="card-header" id="headingOne">
                              <h5 class="mb-0">
                                  <a class="" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                      <h4 class="bold no-margin"><span><?php echo _l('related_knowledgebase_articles'); ?></span></h4>
                                  </a>
                              </h5>
                          </div>
                          <hr>
                          <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                              <div class="card-body">
                                  <ul class="mtop10 articles_list">
                                      <?php foreach($related_articles as $rel_article_article) { ?>
                                          <li>
                                              <i class="fa fa-file-text-o"></i>
                                              <a href="<?php echo admin_url('knowledge_base/view/'.$rel_article_article['slug']); ?>" class="article-heading"><?php echo $rel_article_article['subject']; ?></a>
                                              <div class="text-muted mtop10"><?php echo strip_tags(mb_substr($rel_article_article['description'],0,100)); ?>...</div>
                                          </li>
                                          <hr />
                                      <?php } ?>
                                  </ul>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
<!--                <h4 class="bold no-margin">--><?php //echo _l('related_knowledgebase_articles'); ?><!--</h4>-->
<!--                <hr />-->
<!--                <ul class="mtop10 articles_list">-->
<!--                --><?php //foreach($related_articles as $rel_article_article) { ?>
<!--                    <li>-->
<!--                        <i class="fa fa-file-text-o"></i>-->
<!--                        <a href="--><?php //echo admin_url('knowledge_base/view/'.$rel_article_article['slug']); ?><!--" class="article-heading">--><?php //echo $rel_article_article['subject']; ?><!--</a>-->
<!--                        <div class="text-muted mtop10">--><?php //echo strip_tags(mb_substr($rel_article_article['description'],0,100)); ?><!--...</div>-->
<!--                    </li>-->
<!--                    <hr />-->
<!--                    --><?php //} ?>
<!--                </ul>-->
            </div>
        </div>
    </div>
    <?php } ?>
</div>
</div>
<?php init_tail(); ?>
<script>
    $(function(){
       $('.article_useful_buttons button').on('click', function(e) {
           e.preventDefault();
           var data = {};
           data.answer = $(this).data('answer');
           data.articleid = '<?php echo $article->articleid; ?>';
           $.post(admin_url+'knowledge_base/add_kb_answer', data).done(function(response) {
               response = JSON.parse(response);
               if (response.success == true) {
                   $(this).focusout();
               }
               $('.answer_response').html(response.message);
           });
       });
   });
</script>
</body>
</html>
