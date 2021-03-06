<?php if($this->session->flashdata('msg')): ?>
<div class="alert alert-success" role="alert">
  <center><?php echo $this->session->flashdata('msg'); ?></center>
</div>
<?php endif; ?>
<?php if($this->session->flashdata('msgreq')): ?>
<div class="alert alert-danger" role="alert">
  <center><?php echo $this->session->flashdata('msgreq'); ?></center>
</div>
<?php endif; ?>
<h1 class="mb-5 font-weight-bold" align="center">You can add new opportunities here</h1>
<main id="edit-profile">
       <div class="edit-profile__container">
           <form action="process" method="post" class="edit-profile__form">
               <div class="form__row">
                   <label for="company" class="form__label">Company / Organisation:</label>
                   <input id="Company" name="company" type="text" class="form__input" />
               </div>
               <div class="form__row">
                   <label for="title" class="form__label">Title:</label>
                   <input id="title" type="text" name="title" class="form__input" />
               </div>
               <div class="form__row">
                   <label for="website" class="form__label">Website:</label>
                   <input id="website" type="url" name="website" class="form__input" />
               </div>
               <div class="form__row">
                   <label for="location" class="form__label">Location:</label>
                   <input id="location" type="text" name="location" class="form__input" />
               </div>
               <div class="form__row">
                   <label for="last-date" class="form__label">Last date:</label>
                   <input id="last-date" type="date" name="last-date" class="form__input" />
               </div>
               <div class="form__row">
                   <label for="email" class="form__label">Company Email:</label>
                   <input id="email" type="email" name="email" class="form__input" />
               </div>
               <div class="form__row">
                   <label for="bio" class="form__label">More Details:</label>
                   <textarea id="bio" name="moreInfo"></textarea>
               </div>
               <input type="submit" value="Submit">
           </form>
       </div>
   </main>
