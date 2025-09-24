<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
$key = ( !empty( $value['form'] ) ? sanitize_key( $value['form'] ) : null );
switch ( $key ) {
    case 'advertising-form':
        ob_start();
        ?>
[section_break section_break-493 "Advertising Form"]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Last Name <span class="required">*</span>
            [text* last-name] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Email <span class="required">*</span>
            [email* email autocomplete:email] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Phone <span class="required">*</span>
            [phone* phone-550] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Company Name <span class="required">*</span>
            [text* company-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Website
            [url url-942] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Price <span class="required">*</span>
    [select* price first_as_label "Select" "$25/month" "$50/month" "$75/month" "$100/month"] </label>

<label> Additional Message 
    [textarea additional-msg x3] </label>

[submit "Submit"]
    <?php 
        $data = ob_get_clean();
        break;
    case 'attendance-form':
        ob_start();
        ?>
[section_break section_break-359 "Are you coming?"] You can RSVP for others too. Just do it one at a time please. [/section_break]

<label> Can You Attend? <span class="required">*</span>
    [select* attend first_as_label "Select" "Yes, I'll be there!" "Can't make it."]</label>

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Last Name <span class="required">*</span>
            [text* last-name] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label>  E-mail <span class="required">*</span>
            [email* your-email autocomplete:email] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> phone <span class="required">*</span>
            [phone* phone-550] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Additional Message
    [textarea additional-message x3] </label>

[submit "Submit"]
    <?php 
        $data = ob_get_clean();
        break;
    case 'attendance-certificate-form':
        ob_start();
        echo esc_html__( 'Please upgrade to pro use this form.', 'essential-addons-for-contact-form-7' );
        $data = ob_get_clean();
        break;
    case 'black-friday-deals-submission-form':
        ob_start();
        echo esc_html__( 'Please upgrade to pro use this form.', 'essential-addons-for-contact-form-7' );
        $data = ob_get_clean();
        break;
    case 'mailing-address':
        ob_start();
        ?>
[section_break section_break-167 "Mailing Address"] To update our mailing list we need you to fill out this form [/section_break]

<label> Title <span class="required">*</span>
    [select* title first_as_label "Select" "Mr." "Mrs." "Ms." "Miss" "Dr." "Prof."] </label>

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name placeholder "Alex"] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Last Name <span class="required">*</span>
            [text* last-name placeholder "Piter"] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Phone Number <span class="required">*</span>
            [phone* phone-248] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Email Address <span class="required">*</span>
            [email* email-35 placeholder "hello@example.com"] </label>
    [/eacf7-col]
[/eacf7-row]

[address address-539 format:international required_fields:line1|city|state|zip|country]

[submit "Submit"]
    <?php 
        $data = ob_get_clean();
        break;
    case 'leave-request-form':
        ob_start();
        ?>
[section_break section_break-743 "Request for Leave"] Request your leave details down below. [/section_break]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Last Name <span class="required">*</span>
            [text* last-name] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Employee ID
    [text employee-id] </label>

[eacf7-row]
    [eacf7-col col:2]
        <label> Your email <span class="required">*</span>
            [email* your-email autocomplete:email] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Your phone <span class="required">*</span>
            [phone* phone-550] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Company <span class="required">*</span>
            [text* company] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Position <span class="required">*</span>
            [text* position] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Position
    [text position]</label>

<label> Department <span class="required">*</span>
    [text* department]</label>

<label> Manager <span class="required">*</span>
    [text* manager]</label>

[section_break section_break-885 "Details of Leave"]

<label> Leave Type
    [select* leave-type first_as_label "Select Type" "Casual Leave" "Sick Leave"] </label>

<label> Leave Request For
    [select leave-request "Days" "Hours"] </label>

<label> Leave Start Date (Days)
    [date leave-start-date] </label>

<label> Leave End Date (Days)
    [date leave-end-date] </label>

<label> Leave Date (Hours)
    [date leave-date] </label>

<label> Leave Hour (Hours)
    [text leave-hour] </label>

<label> Comments
    [textarea comments x3] </label>

[submit "Submit"]
    <?php 
        $data = ob_get_clean();
        break;
    case 'appointment-request-form':
        ob_start();
        echo esc_html__( 'Please upgrade to pro use this form.', 'essential-addons-for-contact-form-7' );
        $data = ob_get_clean();
        break;
    case 'doctor-appointment-form':
        ob_start();
        echo esc_html__( 'Please upgrade to pro use this form.', 'essential-addons-for-contact-form-7' );
        $data = ob_get_clean();
        break;
    case 'simple-application-form':
        ob_start();
        ?>
[section_break section_break-446 "Sample Application Form"] Fill the form below accurately indicating your potentials and suitability to job applying for. [/section_break]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name placeholder "Alex"] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Last Name <span class="required">*</span>
            [text* last-name placeholder "Piter"] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Phone Number <span class="required">*</span>
            [phone* phone-248] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Email Address <span class="required">*</span>
            [email* email-35 placeholder "hello@example.com"] </label>
    [/eacf7-col]
[/eacf7-row]

[address* address-105 format:international required_fields:line1|city|state|zip|country]

<label> How were you referred to us? <span class="required">*</span>
    [select* select-521 first_as_label "Select" "Walk-In" "Employee" "Newspaper Ad" "Google" "Facebook" "Twitter"]

<label> Position <span class="required">*</span>
    [select* position first_as_label "select" "Account Manager" "AR/VR Developer" "Backend Developer" "Content Marketer" "Customer Success Manager" "Digital Marketing Specialist" "Embedded Systems Developer" "Frontend Developer" "Game Developer" "Graphic Designer" "Help Desk Analyst" "Interaction Designer" "Mobile App Developer" "Motion Graphics Designer" "Operations Manager" "Product Designer" "Sales Engineer" "SEO Specialist" "Software Developer" "Social Media Manager" "Technical Support Engineer" "UI Designer" "UX Designer" "Visual Designer" "Web Designer" "Full Stack Developer"] </label>

<label> Cover Letter <span class="required">*</span>
    [textarea* cover-letter x3] </label>

<label> Upload Resume <span class="required">*</span>
    [file_upload* file_upload-767 max_files:1 media_library] </label>

[section_break section_break-960 "References"]

<label> Refference 1 <span class="required">*</span>
    [textarea* refference-1 x3] </label>

<label> Refference 2 <span class="required">*</span>
    [textarea* refference-2 x3] </label>

[submit "Submit Application"]
    <?php 
        $data = ob_get_clean();
        break;
    case 'simple-contact-form':
        ob_start();
        ?>
[section_break section_break-840 "Contact"]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Last Name <span class="required">*</span>
            [text* last-name] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Your email <span class="required">*</span>
    [email* your-email autocomplete:email] </label>

<label> Your phone <span class="required">*</span>
    [phone* phone-550] </label>

<label> Your Website <span class="required">*</span>
    [url* url-416] </label>

<label> Subject <span class="required">*</span>
    [text* your-subject] </label>

<label> Message <span class="required">*</span>
    [textarea* message x3] </label>

[submit "Submit"]
    <?php 
        $data = ob_get_clean();
        break;
    case 'employee-information-form':
        ob_start();
        echo esc_html__( 'Please upgrade to pro use this form.', 'essential-addons-for-contact-form-7' );
        $data = ob_get_clean();
        break;
    case 'frontend-post-submission':
        ob_start();
        echo esc_html__( 'Please upgrade to pro use this form.', 'essential-addons-for-contact-form-7' );
        $data = ob_get_clean();
        break;
    case 'blog-newsletter-subscription-form':
        ob_start();
        ?>
[section_break section_break-688 "Newsletter"] Subscribe to the TRANSFORM Newsletter and receive exclusive offers, updates and fitness news. [/section_break]

<label> Email Address <span class="required">*</span>
    [email* email-address] </label>

[submit "Subscribe"]
<?php 
        $data = ob_get_clean();
        break;
    case 'blood-donation-form':
        ob_start();
        ?>
[section_break section_break-453 "Blood Donation Form"] Confidential - Please answer the following questions correctly. This will help to protect you and the patient who receives your blood. [/section_break]

<label> What is your blood type? <span class="required">*</span>
    [select* blood-type include_blank "O+" "O-" "A+" "A-" "B+" "B-" "AB+" "AB-"] </label>

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name autocomplete:first-name placeholder "First Name"] 
        </label> 
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Last Name <span class="required">*</span>
            [text* last-name autocomplete:last-name placeholder "Last Name"] </label>  
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Date of Birth <span class="required">*</span>
            [date* date-of-birth placeholder "dd/mm/yy"] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Gender <span class="required">*</span>
            [select* select-230 first_as_label "Male" "Female"] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Occupation 
    [text occupation placeholder "Job Holder/ Student"] </label>

[eacf7-row]
    [eacf7-col col:2]
        <label> Phone Number <span class="required">*</span>
            [phone* phone-248] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Email Address <span class="required">*</span>
            [email* email-35 placeholder "hello@example.com"] </label>
    [/eacf7-col]
[/eacf7-row]

[address* address-965 format:international required_fields:line1|city|state|zip|country]

[eacf7-row]
    [eacf7-col col:2]
        <label> Weight
            [text weight] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Pulse
            [text pulse] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Hb
            [text hb] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> BP
            [text bp] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Temparature
            [text temparature] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> What was the last time you donated blood? <span class="required">*</span>
            [date* donate-last-time] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Have you donated previously? <span class="required">*</span>
     [select* select-692 first_as_label "Yes" "No"] </label>

<label> In the last six months have you had any of the following? </label>
[checkbox checkbox-601 use_label_element "Tattooing" "Ear piercing" "Dental extraction"]

<label> Do you suffer from or have suffered from any of the following diseases? </label>
[checkbox checkbox-602 use_label_element "Heart Disease" "Diabetes" "Sexually Transmitted Diseases" "Cancer/Malignant Disease" "Hepatitis B/C" "Typhoid ( last on year) (Antay joro)" "Lung Disease" "Allergic Disease" "Kidney Disease" ]

<label> Are you taking or have you taken any of these in the past 72 hours? </label>
[checkbox taking-medicine use_label_element "Antibiotics" "Steroids" "Aspirin" "Vaccinations" "Alcohol" ]

<div>
[submit "Submit Form"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'hotel-booking-form':
        ob_start();
        echo esc_html__( 'Please upgrade to pro use this form.', 'essential-addons-for-contact-form-7' );
        $data = ob_get_clean();
        break;
    case 'non-profit-dinner-rsvp-form':
        ob_start();
        ?>
[section_break section_break-453 "Non-Profit Dinner RSVP Form"] A dinner for refugee children![/section_break]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name autocomplete:first-name placeholder "First Name"] 
        </label> 
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Last Name <span class="required">*</span>
            [text* last-name autocomplete:last-name placeholder "Last Name"] </label>  
    [/eacf7-col]
[/eacf7-row]
[eacf7-row]
    [eacf7-col col:2]
        <label> Phone
            [tel phone placeholder "Phone Number"] 
        </label>
    [/eacf7-col]
    [eacf7-col col:2] 
        <label>Email
            [email email placeholder "Email Address"] 
        </label>
    [/eacf7-col]
[/eacf7-row]

<label> Are you attending?
    [radio attend-date use_label_element class:eacf7-radio default:1 "Yes" "No"] 
</label>

<label> Total Guests Attending
    [number total-guest min:1 placeholder "1-3"]  
</label> 

<label> Special Note <span class="required">*</span>
    [textarea* special-note x3] 
</label> 

<div>
    [submit "Submit Form"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'installation-check-form':
        ob_start();
        ?>
[section_break section_break-453 "Installation Check Form"]

<label>Installation Engineers Name <span class="required">*</span>
    [text* installation-eng-name] </label>

<label>Customer Name <span class="required">*</span>
    [text* customer-name] </label>

[address* customer-address format:international required_fields:line1|city|state|zip|country]

<label>Start Date & Time <span class="required">*</span>
    [date_time* start-date_time] </label>

<label>Type of Work <span class="required">*</span>
    [select* type-work first_as_label "Select" "1st Fix Tagging System" "2nd Fix Tagging System" "Service Call Tagging" "Site Survey" "Attend Site Meeting" "PPM visit" "Other"] </label>

<label>What work was carried out ? <span class="required">*</span>
    [textarea* work-carried-out x3] 
</label>

<label>Further Action Required
    [textarea further-action-required x3] </label>

<label>Serial Numbers or Parts Used
    [textarea serian-numbers-or-parts x3] </label>

<label>Photo
    [image_upload image_upload-931 max_files:1] </label>

<label>End Date & Time <span class="required">*</span>
    [date_time* end-date_time] </label>

<div>
    [submit "Submit"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'client-survey-form':
        ob_start();
        ?>
[section_break section_break-978 "Clients, we hear you!"] When you have a problem, we’ll do our best to fix it. When you have a great idea we look into how we can make it happen. [/section_break]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name autocomplete:first-name placeholder "First Name"] 
        </label> 
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Last Name <span class="required">*</span>
            [text* last-name autocomplete:last-name placeholder "Last Name"] </label>  
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Phone Number <span class="required">*</span>
            [phone* phone-248] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Email Address <span class="required">*</span>
            [email* email-35 placeholder "hello@example.com"] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Your first impression about our company?
    [textarea first-impression x3] </label>

<label> Do you find our work force technically sound?
    [textarea techincally-sound x3] </label>

<label> Does our work force have good communication skills?
    [textarea communication-skills x3] </label>

<label> How do you find the quality of service provided?
    [textarea quality-service x3] </label>

<label> Are you kept updated about our company’s current happenings?
    [textarea current-happenings x3] </label>

<label> Does our workforce act pro-actively?
    [textarea pro-activity x3] </label>

<label> How did you find the company’s overall atmosphere?
    [textarea overall-atmosphere x3] </label>

<label> Complaints (If Any)
    [textarea complaints x3] </label>

<label> Areas in which we can improve upon
    [textarea improve-upon x3] </label>

<label> Final Comments (If Any)
    [textarea final-comments x3] </label>

<label> Overall rating on the scale of 1 to 5
    [rating* rating-542 icon:star1 style:0 default:5]</label>

<div>
    [submit "Submit Form"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'conference-registration':
        ob_start();
        ?>
[section_break section_break-978 "Conference Registration"]

<p>Thank you for registering to attend the Local Annual Conference. We want to make the process as easy as possible. You will need to complete a separate registration form for each individual attending.</p>

<p>If you have any questions about registration, please call 1-800-000-0000.</p>

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name autocomplete:first-name placeholder "First Name"] 
        </label> 
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Last Name <span class="required">*</span>
            [text* last-name autocomplete:last-name placeholder "Last Name"] </label>  
    [/eacf7-col]
[/eacf7-row]

[address* address-952 format:international required_fields:line1|city|state|zip|country]

[eacf7-row]
    [eacf7-col col:2]
        <label> Phone Number <span class="required">*</span>
            [phone* phone-248] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Email Address <span class="required">*</span>
            [email* email-35 placeholder "hello@example.com"] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Comments (If Any)
    [textarea comments x3] </label>

<div>
    [submit "Submit Form"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'document-verification-form':
        ob_start();
        ?>
[section_break section_break-743 "Document Verification Form"] Please provide the following information for document verification. [/section_break]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Last Name <span class="required">*</span>
            [text* last-name] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Your email <span class="required">*</span>
            [email* your-email autocomplete:email] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Your phone <span class="required">*</span>
            [phone* phone-550] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Document Type <span class="required">*</span>
            [select* document-type first_as_label "Select" "Driver's License" "ID Card" "Passport" "Other"] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Document Number <span class="required">*</span>
            [text* document-number] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Upload Document
    [file_upload document max_files:1] </label>

<label> Additional Comments
    [textarea additional-comments x3] </label>

[submit "Submit"]
    <?php 
        $data = ob_get_clean();
        break;
    case 'simple-conversational-form':
        ob_start();
        ?>
[conversational_start conversational_start-1 step:1 "What’s your name?"]
    [text* your-name autocomplete:your-name placeholder "Your Name"]
[conversational_end conversational_end-1]

[conversational_start conversational_start-1 step:1 "What’s your email?"]
    [email* your-email autocomplete:email placeholder "Email Address"]
[conversational_end conversational_end-2]

[conversational_start conversational_start-1 step:1 "Can you share your phone number?"]
    [tel* tel-547 placeholder "Phone Number"]
[conversational_end conversational_end-3]

[conversational_start conversational_start-1 step:1 "How can we help you?"]
    [textarea* your-message placeholder "Your Message"]
    [submit "Submit Form"]
[conversational_end conversational_end-4]
    <?php 
        $data = ob_get_clean();
        break;
    case 'employee-complaint-form':
        ob_start();
        ?>
[section_break section_break-978 "Employee Complaint"] Tell us what happened in the form below. [/section_break]

[eacf7-row]
    [eacf7-col col:2]
        <label> Employee First Name <span class="required">*</span>
            [text* first-name autocomplete:first-name placeholder "First Name"] 
        </label> 
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Employee Last Name <span class="required">*</span>
            [text* last-name autocomplete:last-name placeholder "Last Name"] </label>  
    [/eacf7-col]
[/eacf7-row]

<label> Date of Complaint <span class="required">*</span>
    [date* date-153] </label>

<label> Team Leader/ Supervisor Name <span class="required">*</span>
    [text* team-leader] </label>

<label> Describe accurately the details of your complaint and against whom:
    [textarea complaint x3] </label>

<label> Describe how the incident you are complaining about has impacted negatively on your work:
    [textarea incident x3] </label>

<label> Describe how the company can deal effectively with your complaint:
    [textarea deal-effectively x3] </label>

<label> Give additional comments which you believe will be important during further investigations of your complaint:
    [textarea additional-comments x3] </label>

<label> Team Leader/ Supervisor’s comments:
    [textarea team-leader-comments x3] </label>

<div>
    [submit "Submit Form"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'employee-nomination-form':
        ob_start();
        ?>
[section_break section_break-359 "Employee Nomination Form"] Please fill out the form to nominate an employee for recognition. [/section_break]

[eacf7-row]
    [eacf7-col col:2]
        <label> Nominator's First Name <span class="required">*</span>
            [text* first-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Nominator's Last Name <span class="required">*</span>
            [text* last-name] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label>  E-mail <span class="required">*</span>
            [email* your-email autocomplete:email] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> phone <span class="required">*</span>
            [phone* phone-550] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Nominee's Full Name <span class="required">*</span>
    [text* nominee-full-name] </label>

<label> Nominee's Job Title <span class="required">*</span>
    [text* nominee-job-title] </label>

<label> Nominee's Department <span class="required">*</span>
    [text* nominee-dept] </label>

<label> Reason for Nomination
    [textarea reason-for-nomination x3] </label>

<label> Specific Contributions
    [textarea specific-contributions x3] </label>

<label> How has the nominee demonstrated exceptional performance?
    [textarea exceptional-performance x3] </label>

<label> Additional Comments
    [textarea additional-comments x3] </label>

[submit "Submit"]
    <?php 
        $data = ob_get_clean();
        break;
    case 'it-service-ticket-form':
        ob_start();
        ?>
[section_break section_break-743 "IT Service Ticket"] Please provide the details of the problem. [/section_break]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Last Name <span class="required">*</span>
            [text* last-name] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Your email <span class="required">*</span>
            [email* your-email autocomplete:email] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Your phone <span class="required">*</span>
            [phone* phone-550] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Department <span class="required">*</span>
            [text* department] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> User ID <span class="required">*</span>
            [text* user-id] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Upload Screenshot
    [image_upload image_upload-822 max_files:1]</label>

<label> Describe the Problem 
    [textarea problem x3] </label>

[submit "Submit"]
    <?php 
        $data = ob_get_clean();
        break;
    case 'identity-verification-form':
        ob_start();
        echo esc_html__( 'Please upgrade to pro use this form.', 'essential-addons-for-contact-form-7' );
        $data = ob_get_clean();
        break;
    case 'online-complaint-form':
        ob_start();
        ?>
[section_break section_break-978 "We are here to assist you!"] Please complete the form below for your complaints. [/section_break]

<label> Date of filling the form: <span class="required">*</span>
    [date* date-153] </label>

<label> Complainant's Name: <span class="required">*</span>
    [text* complainant-name] </label>

<label> E-mail <span class="required">*</span>
    [email* email] </label>

<label> The complaint is regarding:
    [textarea complaint-regarding x3] </label>

<label> The nature of complaint:
    [textarea nature-complaint x3] </label>

<label> Name of the company/person against which/whom the complaint is filed:
    [text company-against] </label>

<label> The specific details of the complaint:
    [textarea specific-details x3] </label>

<div>
    [submit "Send"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'online-donation-form':
        ob_start();
        ?>
[section_break section_break-453 "Online Donation Form"]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name autocomplete:first-name placeholder "First Name"] 
        </label> 
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Last Name <span class="required">*</span>
            [text* last-name autocomplete:last-name placeholder "Last Name"] </label>  
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Phone Number <span class="required">*</span>
            [phone* phone-248] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Email Address <span class="required">*</span>
            [email* email-35 placeholder "hello@example.com"] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Type of Donation <span class="required">*</span>
     [select* select-692 first_as_label "Love Offering" "Building Expansion" "One Time Donation"] </label>

<label> Comments <span class="required">*</span>
     [textarea* comments x3] </label>

<label> Donation Amount <span class="required">*</span>
     [text* amount] </label>

<label> Payment Methods <span class="required">*</span>
     [select* payment-methods first_as_label "Select" "Bank" "Cash" "Credit Card" "PayPal"] </label>

[submit "Submit Form"]
    <?php 
        $data = ob_get_clean();
        break;
    case 'online-event-registration':
        ob_start();
        ?>
[section_break section_break-440 "Event Registration"] Be Part of the Story – Register Today! [/section_break]

<h3>Contact Information</h3>

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name autocomplete:first-name placeholder "First Name"] 
        </label> 
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Last Name <span class="required">*</span>
            [text* last-name autocomplete:last-name placeholder "Last Name"] </label>  
    [/eacf7-col]
[/eacf7-row]

[address* address-511 format:international required_fields:line1|city|state|zip|country]

[eacf7-row]
    [eacf7-col col:2]
        <label> Phone Number <span class="required">*</span>
            [phone* phone-248] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Email Address <span class="required">*</span>
            [email* email-35 placeholder "hello@example.com"] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Website
     [url url-682] </label>

<h3>Event Information</h3>

<label> Please select the type of registration you would like
     [select select-210 first_as_label "Select" "Full Conference Registration" "One Day Registration" "Speaker Full Conference Registration" "Speaker One Day Registration" "Exhibitor Full Conference Registration" "Exhibitor One Day Registration" "Student Full Conference Registration" "Student One Day Registration"] </label>

<h3>Meals</h3>
<p>All Full Conference Registration fees include the following meals:</p>

[section_break section_break-825] Monday: Continental breakfast and lunch Tuesday: Continental breakfast and lunch Wednesday: Continental breakfast [/section_break]

<label> Dietary Needs </label>
[radio radio-737 use_label_element "Vegetarian Meals" "Vegan Meals" "Dairy Free Meals" "Non-Vegetarian Meals"]

<div>
[submit "Submit Form"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'simple-feedback-form':
        ob_start();
        ?>
[section_break section_break-440 "Feedback Form"]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name autocomplete:first-name placeholder "First Name"] 
        </label> 
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Last Name <span class="required">*</span>
            [text* last-name autocomplete:last-name placeholder "Last Name"] </label>  
    [/eacf7-col]
[/eacf7-row]

<label> Please provide your feedback on the quality of our service.
     [radio radio-460 use_label_element "Excellent" "Very Good" "Good" "Average" "Poor"] </label>

<label> Do you have suggestions on what we can do to provide you with a better service?
     [textarea suggestion x3] </label>

<div>
[submit "Submit"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'item-request-form':
        ob_start();
        ?>
[section_break section_break-751 "Item Request Form"] Please fill out this form to request an item. [/section_break]

[section_break section_break-868 "Requester's Information"]

[eacf7-row]
    [eacf7-col col:2]
        <label> Requester's First Name <span class="required">*</span>
            [text* your-first-name autocomplete:name placeholder "First Name"] 
        </label> 
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Requester's Last Name <span class="required">*</span>
            [text* your-last-name autocomplete:last-name placeholder "Last Name"] 
        </label>  
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Phone
            [tel phone placeholder "Phone Number"] 
        </label>
    [/eacf7-col]
    [eacf7-col col:2] 
        <label>Email
            [email email placeholder "Email Address"] 
        </label>
    [/eacf7-col]
[/eacf7-row]

<label> Department
    [text dept] </label>

[section_break section_break-303 "Item Details"]

<label> Item Name
    [text item-name]</label>

<label> Item Description
    [textarea item-description x3]</label>

<label> Quantity
    [text quantity]</label>

<label> Urgency of Request
    [select select-350 first_as_label "Select" "Low" "Medium" "High"]</label>

<label> Delivery Address</label>
[address* address-347 format:international required_fields:line1|city|state|zip|country]

<label> Preferred Delivery Date
    [date date-98]</label>

<label> Additional Notes
    [textarea additional-notes x3]</label>

<div>
    [submit "Submit"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'general-inquiry-form':
        ob_start();
        ?>
[section_break section_break-868 "General Inquiry Form"]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* your-first-name autocomplete:name placeholder "First Name"] 
        </label> 
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Last Name <span class="required">*</span>
            [text* your-last-name autocomplete:last-name placeholder "Last Name"] 
        </label>  
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Phone
            [tel phone placeholder "Phone Number"] 
        </label>
    [/eacf7-col]
    [eacf7-col col:2] 
        <label>Email
            [email email placeholder "Email Address"] 
        </label>
    [/eacf7-col]
[/eacf7-row]

<label> Inquiry
    [textarea inquiry x3]</label>

<div>
    [submit "Submit"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'service-request-form':
        ob_start();
        ?>
[section_break section_break-26 "Service Request"] Please fill out the form and press "Submit" at the bottom of the form. We will receive you request, process it and answer you within 24 hours regarding to following activities. [/section_break]

[section_break section_break-267 "Property Details"]

<label> Property Name <span class="required">*</span>
    [text* property-name]</label>

[address address-118 format:international required_fields:line1|city|state|zip|country]

[section_break section_break-737 "Contact Person Details"]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* your-first-name autocomplete:name placeholder "First Name"] 
        </label> 
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Last Name <span class="required">*</span>
            [text* your-last-name autocomplete:last-name placeholder "Last Name"] 
        </label>  
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Phone
            [tel phone placeholder "Phone Number"] 
        </label>
    [/eacf7-col]
    [eacf7-col col:2] 
        <label>Email
            [email email placeholder "Email Address"] 
        </label>
    [/eacf7-col]
[/eacf7-row]

[section_break section_break-99 "Service Requirements"]

<label> Description of Problem <span class="required">*</span>
    [textarea problem x3]</label>

<label> Image
    [image_upload image_upload-668 max_files:1 media_library]</label>

[section_break section_break-832 "Availability"]

<label> Preferred Date & Time 1<span class="required">*</span>
    [date_time* date_time-1]</label>

<label> Preferred Date & Time 2<span class="required">*</span>
    [date_time* date_time-2]</label>

<label> Preferred Date & Time 3<span class="required">*</span>
    [date_time* date_time-3]</label>

<div>
    [submit "Submit"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'newsletter-registration-form':
        ob_start();
        ?>
[section_break section_break-267 "Newsletter Registration Form"]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name
            [text your-first-name autocomplete:name placeholder "First Name"] 
        </label> 
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Last Name
            [text your-last-name autocomplete:last-name placeholder "Last Name"] 
        </label>  
    [/eacf7-col]
[/eacf7-row]

<label>Email
    [email email placeholder "Email Address"] 

<label> Please select the newsletters you want to receive.</label>
[checkbox checkbox-341 use_label_element "Features Newsletter" "Announcements" "Industry News" "General Updates"]

<label> In which format do you prefer to receive the newsletters?
    [radio radio-309 use_label_element "Text" "HTML"]</label>

<div>
    [submit "Submit"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'straw-poll-form':
        ob_start();
        ?>
[section_break section_break-267 "Straw Poll"]

<label>1. Who are you voting for?
    [select select-460 first_as_label "Alex Piter" "Jhon Doe" "Michal Doe" "Other"] </label>

<label> 2. Which areas are most important to you when you vote?</label>
[checkbox checkbox-341 use_label_element "Crime & Justice" "Culture" "Environment" "Family & Equality" "Health" "Education" "Immigration" "Infrastructure" "Labor and Business" "Military & Defense" "Taxes & Economy" "Pension & Benefits" ]

<label> 3. What is your age range? </label>
[radio radio-309 use_label_element "18-24" "25-34" "35-45" "46+"]

<label> 4. What is the highest degree or level of school you have completed? </label>
[radio radio-310 use_label_element "High school" "Associate degree (e.g. AA, AS)" "Bachelor’s degree (e.g. BA, BS)" "Master’s degree (e.g. MA, MS, MEd)" "Professional degree (e.g. MD, DDS, DVM)" "Doctorate (e.g. PhD, EdD)"]

<div>
    [submit "Submit"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'woocommerce-product-review':
        ob_start();
        echo esc_html__( 'Please upgrade to pro use this form.', 'essential-addons-for-contact-form-7' );
        $data = ob_get_clean();
        break;
    case 'general-product-review':
        ob_start();
        echo esc_html__( 'Please upgrade to pro use this form.', 'essential-addons-for-contact-form-7' );
        $data = ob_get_clean();
        break;
    case 'service-rating-form':
        ob_start();
        ?>
[section_break section_break-177 "Service Rating"] Here at Local it is our duty to serve you, the customer, and we take your feedback very seriously. Whether negative or positive, please let us know about your experience [/section_break]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name
            [text your-first-name autocomplete:name placeholder "First Name"] 
        </label> 
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Last Name
            [text your-last-name autocomplete:last-name placeholder "Last Name"] 
        </label>  
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Phone
            [phone* phone-172]
        </label>
    [/eacf7-col]
    [eacf7-col col:2] 
        <label>Email
            [email email placeholder "Email Address"] 
        </label>
    [/eacf7-col]
[/eacf7-row]

<label> Please provide your feedback on the quality of our service <span class="required">*</span>
    [rating* rating selected:3 star1:1 star2:2 star3:3 star4:4 star5:5 "default"]  </label> 

<label> Do you have suggestions on what we can do to provide you with a better service?
    [textarea suggestions x3] </label>

[submit "Submit"]
    <?php 
        $data = ob_get_clean();
        break;
    case 'bug-report-form':
        ob_start();
        ?>
[section_break section_break-675 "Bug Report Form Template"] Please use this form to report any bugs or issues you encounter while using our website or application. [/section_break]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* your-first-name autocomplete:name placeholder "First Name"] 
        </label> 
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Last Name <span class="required">*</span>
            [text* your-last-name autocomplete:last-name placeholder "Last Name"] 
        </label>  
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Department <span class="required">*</span>
            [text* department]
        </label>
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Email <span class="required">*</span>
            [email* email placeholder "Email Address"] 
        </label>
    [/eacf7-col]
[/eacf7-row]

<label> Bug Title  <span class="required">*</span>
    [text* bug-title] </label>

<label> Bug Description <span class="required">*</span>
    [textarea* bug-desc x3] </label>

<label> Steps to Reproduce <span class="required">*</span>
    [textarea* steps-reproduce x3] </label>

<label> Screenshot
    [image_upload image_upload-721 max_files:1] </label>

<label>Additional Information
    [textarea additoinal-info x3] </label>

<div>
    [submit "Submit Bug Report"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'simple-repeater-form':
        ob_start();
        echo esc_html__( 'Please upgrade to pro use this form.', 'essential-addons-for-contact-form-7' );
        $data = ob_get_clean();
        break;
    case 'product-quote-form':
        ob_start();
        ?>
[section_break section_break-675 "Get A Quote"]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* your-first-name autocomplete:name placeholder "First Name"] 
        </label> 
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Last Name <span class="required">*</span>
            [text* your-last-name autocomplete:last-name placeholder "Last Name"] 
        </label>  
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Department <span class="required">*</span>
            [text* department]
        </label>
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Email <span class="required">*</span>
            [email* email placeholder "Email Address"] 
        </label>
    [/eacf7-col]
[/eacf7-row]

[address* address-110 format:international required_fields:line1|city|state|zip|country]

<label> Which Product?  <span class="required">*</span>
     [select* select-118 first_as_label "Select" "Product A" "Product B" "Product C" "Product D"]</label>

<label> Quantity <span class="required">*</span>
    [number* number-385] </label>

<label>Additional Information
    [textarea additoinal-info x3] </label>

<div>
    [submit "Submit"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'online-booking-form':
        ob_start();
        echo esc_html__( 'Please upgrade to pro use this form.', 'essential-addons-for-contact-form-7' );
        $data = ob_get_clean();
        break;
    case 'product-order-form':
        ob_start();
        ?>
[section_break section_break-782 "Product Order Form"] Please make sure to fill in the required fields and submit this form to complete your order. [/section_break]

<label> Which Product?  <span class="required">*</span>
     [select* select-118 first_as_label "Select" "Product A" "Product B" "Product C" "Product D"]</label>

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* your-first-name autocomplete:name placeholder "First Name"] 
        </label> 
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Last Name <span class="required">*</span>
            [text* your-last-name autocomplete:last-name placeholder "Last Name"] 
        </label>  
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Phone <span class="required">*</span>
            [phone* phone-338]
        </label>
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Email <span class="required">*</span>
            [email* email placeholder "Email Address"] 
        </label>
    [/eacf7-col]
[/eacf7-row]

[address* address-110 format:international required_fields:line1|city|state|zip|country]

<label> Send Gift? </label>
[radio radio-340 use_label_element "Yes" "No"]

<label> Recipient's Full Name 
    [text recipient-name] </label>

<label> Gift Message 
    [textarea gift-message x3] </label>

<label> Special Instructions
    [textarea special-instructions x3] </label>

<label> Payment Method <span class="required">*</span>
    [select* select-272 first_as_label "Select" "Bank" "Cash" "Credit Card" "Debit Card" "Mobile Banking"] </label>

<div>
    [submit "Submit"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'newsletter-subscription-form':
        ob_start();
        ?>
[section_break section_break-782 "Newsletter Subscription Form"] uninterrupted news source [/section_break]

<label> Sign me up for ... ?  <span class="required">*</span>
     [select* select-118 first_as_label "Select" "Weekly" "Monthly" "Yearly"]</label>

<label> I wish to pay by: ?  <span class="required">*</span>
     [select* select-118 first_as_label "Select" "Bank" "Cash" "Credit Card" "PayPal" "Wise"]</label>

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* your-first-name autocomplete:name placeholder "First Name"] 
        </label> 
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Last Name <span class="required">*</span>
            [text* your-last-name autocomplete:last-name placeholder "Last Name"] 
        </label>  
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Phone <span class="required">*</span>
            [phone* phone-338]
        </label>
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Email <span class="required">*</span>
            [email* email placeholder "Email Address"] 
        </label>
    [/eacf7-col]
[/eacf7-row]

[address* address-110 format:international required_fields:line1|city|state|zip|country]

<div>
    [submit "Submit"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'support-request-form':
        ob_start();
        ?>
[section_break section_break-782 "Support Request Form"]

<label> Order Reference No <span class="required">*</span>
    [text* order-ref] </label>

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* your-first-name autocomplete:name placeholder "First Name"] 
        </label> 
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Last Name <span class="required">*</span>
            [text* your-last-name autocomplete:last-name placeholder "Last Name"] 
        </label>  
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Phone <span class="required">*</span>
            [phone* phone-338]
        </label>
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Email <span class="required">*</span>
            [email* email placeholder "Email Address"] 
        </label>
    [/eacf7-col]
[/eacf7-row]

<label> I'm having a problem with: <span class="required">*</span>
    [select* department first_as_label "Select" "New Order" "Delivery of product" "Billing or charge" "Other"] </label>

<label>  Describe Your Problem <span class="required">*</span>
    [textarea* problem x3] </label>

<label>  File Upload (If Any)
    [file_upload file_upload-690 max_files:1] </label>

<label>  Urgency Level <span class="required">*</span>
    [select* urgency-level first_as_label "Select" "Today" "In the next 48 hours" "This week" "Not urgent"] </label>

<label> How would you like to be contacted? <span class="required">*</span>
    [select* contact first_as_label "Select" "Phone" "Email" "WhatsApp" "Telegram"] </label>

<div>
    [submit "Submit"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'website-survey':
        ob_start();
        ?>
[section_break section_break-316 "Website Survey"] Please fill in the website information [/section_break]

<label> How did you hear about this website? <span class="required">*</span>
    [select* hear-about first_as_label "Select" "Social Media" "Advertising" "Search Engine" "Friend" "Other"] </label>

<label> What browser do you use? <span class="required">*</span>
    [select* browser first_as_label "Select" "Google Chrome" "Firefox" "Safari" "Internet Explorer" "Opera Mini"] </label>

<label> Which device did you use to access the website? <span class="required">*</span>
    [select* device first_as_label "Select" "Desktop/Laptop" "Tablet/Pad" "Mobile"] </label>

<label> Are you satisfied that you found out the website?  <span class="required">*</span>
    [rating* rating-707 icon:star1 style:0 default:5] </label>

<div>
    [submit "Submit"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'volunteer-candidate-registration-form':
        ob_start();
        ?>
[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name autocomplete:first-name placeholder "First Name"] 
        </label>
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Last Name <span class="required">*</span>
            [text* last-name autocomplete:last-name placeholder "Last Name"] 
        </label>  
    [/eacf7-col]
[/eacf7-row]

<label> Email <span class="required">*</span>
    [email* volunteer-email placeholder "Email"]</label>

<label> Contact No <span class="required">*</span>
    [phone* phone-640 validation:1]</label>

<label> Birth Date <span class="required">*</span>
    [date* date-227]</label>

[address* address-703 format:international required_fields:line1|city|state|zip|country]

<label> Please indicate areas to volunteer according to your skills</label>
[checkbox volunteer-skills use_label_element "Hospitals" "Orphanages" "Schools" "Community services" "Computer classes" "Arts and Entertainment"] 

<label> Comments <span class="required">*</span>
    [textarea* volunteer-comments x3]  
</label>

<div>
    [submit "Submit"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'volunteer-recruitment-form':
        ob_start();
        ?>
[section_break section_break-938 "Volunteer Recruitment Form"] Fill in the form below to volunteer to our organization [/section_break]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name autocomplete:first-name placeholder "First Name"] 
        </label>
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Last Name <span class="required">*</span>
            [text* last-name autocomplete:last-name placeholder "Last Name"] 
        </label>  
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Phone <span class="required">*</span>
            [phone* phone-338]
        </label>
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Email <span class="required">*</span>
            [email* email placeholder "Email Address"] 
        </label>
    [/eacf7-col]
[/eacf7-row]

<label> Address </label>
    [address* address-703 format:international required_fields:line1|city|state|zip|country]

<label>  Which days of the week do you want to work? </label>
    [checkbox working-days use_label_element class:eacf7-checkbox "Sunday" "Satarday" "Monday" "Tuesday" "Wednesday" "Thursday" "Friday"]

<label> Enter a range of work hours per week
    [text work-hours]  
</label>

<div>
    [submit "Submit"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'quick-donation-form':
        ob_start();
        ?>
[section_break section_break-453 "Donation Form"]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name autocomplete:first-name placeholder "First Name"] 
        </label> 
    [/eacf7-col]
    [eacf7-col col:2] 
        <label> Last Name <span class="required">*</span>
            [text* last-name autocomplete:last-name placeholder "Last Name"] </label>  
    [/eacf7-col]
[/eacf7-row]

<label> Type of Donation <span class="required">*</span>
     [select* select-692 first_as_label "Select" "Cash" "Product" "Service" "Other"] </label>

<label> Amount <span class="required">*</span>
     [text* amount] </label>

<label> Notes <span class="required">*</span>
     [textarea* notes x3] </label>

<label> Company Name <span class="required">*</span>
     [text* company-name] </label>

[eacf7-row]
    [eacf7-col col:2]
        <label> Phone Number <span class="required">*</span>
            [phone* phone-248] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Email Address <span class="required">*</span>
            [email* email-35 placeholder "hello@example.com"] </label>
    [/eacf7-col]
[/eacf7-row]

[address* address-930 format:international required_fields:line1|city|state|zip|country]

[submit "Submit Form"]
        <?php 
        $data = ob_get_clean();
        break;
    case 'university-admission-form':
        ob_start();
        ?>
<h3>University Enrollment Form</h3>

<label> Anticipated Start Date <span class="required">*</span>
    [date_time date_time-352 "m/d/Y"] </label>

[eacf7-row]
    [eacf7-col col:2]
    <label> First Name <span class="required">*</span>
        [text* your-first-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
    <label> Last Name <span class="required">*</span>
        [text* your-last-name] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Phone
    [phone phone-31] </label>

<label> Email
    [email email] </label>

<label> Date of Birth <span class="required">*</span>
    [date_time* date_time-129 "m/d/Y"] </label>

<label> Gender <span class="required">*</span>
    [radio gender "Male" "Female" "Other"] </label>

[address address-659 format:international required_fields:line1|city|state|zip|country]

<label> Proof of identity (e.g. birth certificate, Passport etc.) <span class="required">*</span>
    [file_upload* file_upload-442 extensions:jpeg|jpg|png|webp|pdf|gif max_size:2 max_files:1]</label>

<h3>Background Information:</h3>

<label> Enrollment Status <span class="required">*</span>
    [radio enrollment-status "Part Time" "Full Time"] </label>

<label> High School Name
    [text high-school] </label>

<label>High School Address</label>
    [address high-school-address format:international required_fields:line1|city|state|zip|country]

<label> GPA <span class="required">*</span>
    [text* gpa] </label>

<label> Diploma Type <span class="required">*</span>
    [text* diploma-type] </label>

<label> High School Transcripts <span class="required">*</span>
    [file_upload* high-school-transcripts extensions:jpeg|jpg|png|webp|pdf|gif max_size:2 max_files:1] </label>

<label> Medical Allergies <span class="required">*</span>
    [text* medical-allergies] </label>

<label> Medical Allergies <span class="required">*</span>
    [text* medical-allergies] </label>

[eacf7-row]
    [eacf7-col col:2]
    <label> Parent/Guardian (First Name) <span class="required">*</span>
        [text* parent-first-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
    <label> Last Name <span class="required">*</span>
        [text parent-last-name] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Parent/Guardian Company <span class="required">*</span>
    [text* parent-company] </label>

<label> Parent/Guardian Phone <span class="required">*</span>
    [phone* parent-phone] </label>

<label> Parent/Guardian Email <span class="required">*</span>
    [email* parent-email] </label>

[submit "Submit Form"]
        <?php 
        $data = ob_get_clean();
        break;
    case 'transcript-request-form':
        ob_start();
        ?>
[eacf7-row]
    [eacf7-col col:2]
    <label> First Name <span class="required">*</span>
        [text* your-first-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
    <label> Last Name <span class="required">*</span>
        [text* your-last-name] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Graduation Date <span class="required">*</span>
    [date_time graduation-date "m/d/Y"] </label>

<label> Address </label>
[address address-659 format:international required_fields:line1|city|state|zip|country]

<label> Registration Number <span class="required">*</span>
    [text* registration-number] </label>

<label> Date of Birth <span class="required">*</span>
    [date_time dob "m/d/Y"] </label>

<label> Current Phone <span class="required">*</span>
    [phone* phone-31] </label>

<label> Email <span class="required">*</span>
    [email* email] </label>

<label> I wish to pick up an UNOFFICIAL copy of my transcript <span class="required">*</span>
    [radio pick-up-transcript "Yes" "No"] </label>

<h3>Please send an OFFICIAL copy of my high school transcript to:</h3>

<label> College/University Name <span class="required">*</span> 
    [text* col-uni-name] </label>

<label> College/University Address </label>
[address col-uni-address format:international required_fields:line1|city|state|zip|country]

<label> Today's Date <span class="required">*</span>
    [date_time today-date "m/d/Y"] </label>

<label> Upload Signature <span class="required">*</span>
    [file_upload* signature extensions:jpeg|jpg|png|webp|pdf|gif max_size:2 max_files:1]</label>

[submit "Submit Form"]
        <?php 
        $data = ob_get_clean();
        break;
    case 'behavior-assessment-multistep-form':
        ob_start();
        echo esc_html__( 'Please upgrade to pro use this form.', 'essential-addons-for-contact-form-7' );
        $data = ob_get_clean();
        break;
    case 'conversational-restaurant-order-form':
        ob_start();
        echo esc_html__( 'Please upgrade to pro use this form.', 'essential-addons-for-contact-form-7' );
        $data = ob_get_clean();
        break;
    case 'multistep-survey-form':
        ob_start();
        echo esc_html__( 'Please upgrade to pro use this form.', 'essential-addons-for-contact-form-7' );
        $data = ob_get_clean();
        break;
    case 'pizza-order-form':
        ob_start();
        echo esc_html__( 'Please upgrade to pro use this form.', 'essential-addons-for-contact-form-7' );
        $data = ob_get_clean();
        break;
    case 'new-customer-registration-form':
        ob_start();
        echo esc_html__( 'Please upgrade to pro use this form.', 'essential-addons-for-contact-form-7' );
        $data = ob_get_clean();
        break;
    case 'course-registration-form':
        ob_start();
        ?>
[section_break section_break-359 "Course Registration Form"] Fill out the form carefully for registration [/section_break]

[eacf7-row]
    [eacf7-col col:2]
        <label> Student First Name <span class="required">*</span>
            [text* first-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Student Last Name <span class="required">*</span>
            [text* last-name] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Birth of Date <span class="required">*</span>
            [date* dob]</label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Gender <span class="required">*</span>
            [select* gender first_as_label "Select" "Male" "Female"] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Address
    [address* address format:international required_fields:line1|city|state|zip|country] </label>

[eacf7-row]
    [eacf7-col col:2]
        <label>  Student E-mail <span class="required">*</span>
            [email* your-email autocomplete:email] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Student phone <span class="required">*</span>
            [phone* phone-550] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Department <span class="required">*</span>
            [select department first_as_label "Select" "Department A" "Department B" "Department C" "Department D"] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Courses <span class="required">*</span>
            [select* course first_as_label "Select" "Cybersecurity" "English 101" "English 102" "History 101" "History 102" "Introduction to Linux" "Introduction to Windows" "Math 101" "Math 102"] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Additional Comments
    [textarea additional-comments x3] </label>

[submit "Submit"]
        <?php 
        $data = ob_get_clean();
        break;
    case 'course-registration-form':
        ob_start();
        ?>
[section_break section_break-743 "Training Course Form"] Participant Registration Form [/section_break]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Last Name <span class="required">*</span>
            [text* last-name] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Your email <span class="required">*</span>
            [email* your-email autocomplete:email] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Your phone <span class="required">*</span>
            [phone* phone-550] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Company <span class="required">*</span>
            [text* company] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Position <span class="required">*</span>
            [text* position] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Address <span class="required">*</span> </label>
[address* address-584 format:international required_fields:line1|city|state|zip|country]

<label> Course List
    [select course first_as_label "Select Course" "Course A" "Course B" "Course C" "Course D" "Coures E"] </label>

[submit "Submit"]
        <?php 
        $data = ob_get_clean();
        break;
    case 'event-sponsorship-form':
        ob_start();
        echo esc_html__( 'Please upgrade to pro use this form.', 'essential-addons-for-contact-form-7' );
        $data = ob_get_clean();
        break;
    case 'flight-reservation-form':
        ob_start();
        echo esc_html__( 'Please upgrade to pro use this form.', 'essential-addons-for-contact-form-7' );
        $data = ob_get_clean();
        break;
    case 'client-consultation-form':
        ob_start();
        echo esc_html__( 'Please upgrade to pro use this form.', 'essential-addons-for-contact-form-7' );
        $data = ob_get_clean();
        break;
    case 'information-request-form':
        ob_start();
        ?>
[section_break section_break-440 "Information Request Form"]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Last Name <span class="required">*</span>
            [text* last-name] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Email <span class="required">*</span>
            [email* your-email autocomplete:email] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Phone <span class="required">*</span>
            [phone* phone-550] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Requesting Information Regarding 
    [textarea requesting-information x3] </label>

<label> Additional Information 
    [textarea additional-information x3] </label>

[submit "Submit"]
    <?php 
        $data = ob_get_clean();
        break;
    case 'project-proposal-form':
        ob_start();
        ?>
[section_break section_break-440 "Project Proposal Form"]

<label> Title of research project proposal
    [text title-of-project] </label>

[section_break section_break-622 "Created by"]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Last Name <span class="required">*</span>
            [text* last-name] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Email <span class="required">*</span>
            [email* your-email autocomplete:email] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Phone <span class="required">*</span>
            [phone* phone-550] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Address
    [address* address-286 format:international required_fields:line1|city|state|zip|country] </label>

[section_break section_break-623 "Created to"]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* created-first-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Last Name <span class="required">*</span>
            [text* created-last-name] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Email <span class="required">*</span>
            [email* created-email autocomplete:email] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Phone <span class="required">*</span>
            [phone* created-phone-550] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Designation
    [text designation]</label>

<label> Department
    [text department]</label>

<label> Date of Submission
    [date date-363]</label>

[submit "Submit"]
    <?php 
        $data = ob_get_clean();
        break;
    case 'car-rental-form':
        ob_start();
        echo esc_html__( 'Please upgrade to pro use this form.', 'essential-addons-for-contact-form-7' );
        $data = ob_get_clean();
        break;
    case 'refferral-program-form':
        ob_start();
        ?>
[section_break section_break-427 "Referral Program"] Get free stuff if your referral becomes our customer [/section_break]

[section_break section_break-929 "Your details"]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Last Name <span class="required">*</span>
            [text* last-name] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Email <span class="required">*</span>
            [email* your-email autocomplete:email] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Phone <span class="required">*</span>
            [phone* phone-550] </label>
    [/eacf7-col]
[/eacf7-row]

[section_break section_break-12 "Referral details"]

[eacf7-row]
    [eacf7-col col:2]
        <label> Referral First Name <span class="required">*</span>
            [text* ref-first-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Referral Last Name <span class="required">*</span>
            [text* ref-last-name] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Referral Email <span class="required">*</span>
            [email* ref-email autocomplete:email] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Referral Phone <span class="required">*</span>
            [phone* ref-phone] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Tell us more about your referral 
    [textarea about-ref x3]</label>

[submit "Submit"]
    <?php 
        $data = ob_get_clean();
        break;
    case 'employee-reference-request-form':
        ob_start();
        echo esc_html__( 'Please upgrade to pro use this form.', 'essential-addons-for-contact-form-7' );
        $data = ob_get_clean();
        break;
    case 'online-petition-form':
        ob_start();
        echo esc_html__( 'Please upgrade to pro use this form.', 'essential-addons-for-contact-form-7' );
        $data = ob_get_clean();
        break;
    case 'restaurant-evaluation-form':
        ob_start();
        ?>
[section_break section_break-585 "Please Review Us"] Please let us know how was the food and service. [/section_break]

<label> Day Visited
    [date* date-4] </label>

<label> Dine In / Take Out
    [select* dine-take first_as_label "Select" "Dine In" "Take Out"] </label>

<label> Food Quality </label>
[radio food-quality use_label_element "Excellent" "Very Good" "Good" "Average" "Poor"]

<label> Overall Service Quality </label>
[radio overall-service-quality use_label_element "Excellent" "Very Good" "Good" "Average" "Poor"]

<label> Cleanliness </label>
[radio cleanliness use_label_element "Excellent" "Very Good" "Good" "Average" "Poor"]

<label> Order Accuracy </label>
[radio order-accuracy use_label_element "Excellent" "Very Good" "Good" "Average" "Poor"]

<label> Speed of Service </label>
[radio speed-service use_label_element "Excellent" "Very Good" "Good" "Average" "Poor"]

<label> Value </label>
[radio value use_label_element "Excellent" "Very Good" "Good" "Average" "Poor"]

<label> Overall Experience </label>
[radio overall-exp use_label_element "Excellent" "Very Good" "Good" "Average" "Poor"]

<label> Any comments, questions or suggestions?
    [textarea comments x3]</label>

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Last Name <span class="required">*</span>
            [text* last-name] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Email
            [email email autocomplete:email] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Phone
            [phone phone-550] </label>
    [/eacf7-col]
[/eacf7-row]

[submit "Submit"]
    <?php 
        $data = ob_get_clean();
        break;
    case 'training-feedback-form':
        ob_start();
        echo esc_html__( 'Please upgrade to pro use this form.', 'essential-addons-for-contact-form-7' );
        $data = ob_get_clean();
        break;
    case 'rsvp-form':
        ob_start();
        ?>
[section_break section_break-585 "RSVP Form"] Please let us know if you will be able to make it. [/section_break]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Last Name <span class="required">*</span>
            [text* last-name] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Email <span class="required">*</span>
            [email* email autocomplete:email] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Phone <span class="required">*</span>
            [phone* phone-550] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Number of people attending
    [select people first_as_label "Select" "1" "2" "3" "4" "5" "6" "7" "8" "9" "10+"] </label>

<label> What are the names of the other people coming, if any?
    [textarea people-names x3] </label>

<label> Anything you want to add?
    [textarea extra x3]</label>

[submit "Submit"]
    <?php 
        $data = ob_get_clean();
        break;
    case 'video-submit-form':
        ob_start();
        ?>
[section_break section_break-585 "Submit a Video"] Submit your video for evaluation [/section_break]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Last Name <span class="required">*</span>
            [text* last-name] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Email <span class="required">*</span>
            [email* email autocomplete:email] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Phone <span class="required">*</span>
            [phone* phone-550] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Upload Video
    [file_upload file_upload-924 max_files:1] </label>

[section_break section_break-40] Ensure the video is under 10 minutes, uses licensed music and images, and avoids inappropriate content. [/section_break]

<label> Do You Agree to the Terms Above? <span class="required">*</span>
    [select* select-368 first_as_label "Select" "Yes" "No"] </label>

<label> Comments
    [textarea comments x3]</label>

[submit "Submit"]
    <?php 
        $data = ob_get_clean();
        break;
    case 'job-application-form':
        ob_start();
        ?>
[section_break section_break-585 "Job Application Form"] Please Fill Out the Form Below to Submit Your Job Application! [/section_break]

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Last Name <span class="required">*</span>
            [text* last-name] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Email <span class="required">*</span>
            [email* email autocomplete:email] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Phone <span class="required">*</span>
            [phone* phone-550] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Applied Position <span class="required">*</span>
            [text* position] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Earliest Possible Start Date
            [date* date-242] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Preferred Interview Date Time
    [date_time date_time-396] </label>

<label>  Cover Letter
    [textarea cover-later x3] </label>

<label>  Upload Resume <span class="required">*</span>
    [file_upload* file_upload-431 max_files:1] </label>

<label>  Any Other Documents to Upload 
    [file_upload* other-document max_files:2] </label>

[submit "Submit"]
    <?php 
        $data = ob_get_clean();
        break;
    case 'order-cancellation-form':
        ob_start();
        ?>
[section_break section_break-585 "Order Cancellation Form"]

<label> Order Number/ID <span class="required">*</span>
    [text* order-number] </label>

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Last Name <span class="required">*</span>
            [text* last-name] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Email <span class="required">*</span>
            [email* email autocomplete:email] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Phone <span class="required">*</span>
            [phone* phone-550] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Address <span class="required">*</span>
    [address* address-75 format:international required_fields:line1|city|state|zip|country]</label>

<label> Do you want to cancel or postpone your order?  </label>
[radio radio-63 use_label_element "I want to cancel my order." "I want to postpone my order."]

<label>  Please select the start date interval for the delivery of your postponed order
    [date start-date] </label>

<label>  Please select the end date interval for the delivery of your postponed order
    [date end-date] </label>

<label>  Reason for Cancellation/Postponement
    [textarea reason-cancellation x3] </label>

[acceptance acceptance-990] I agree to terms & conditions. [/acceptance]

<div>
    [submit "Submit"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'support-satisfaction-survey-form':
        ob_start();
        ?>
[section_break section_break-699 "Support Satisfaction Survey"] Please fill out this support satisfaction survey [/section_break]

<label> Was the support useful? <span class="required">*</span>
    [select select-51 first_as_label "Select" "Very usefull" "Usefull" "Average" "Not usefull" "Not usefull at all"] </label>

<label> How long did it take for the support team to respond to you?
    [select support-time first_as_label "Select" "0-10 mins" "11-30 mins" "1-2 hours" "3-6 hours" "+6"] </label>

<label> Overall Knowledge
    [select overall-knowledge first_as_label "Select" "Very Good" "Good" "Average" "Poor" "Very Poor"] </label>

<label> Solving Method
    [select solving-method first_as_label "Select" "Very Good" "Good" "Average" "Poor" "Very Poor"] </label>

<label> Clarity
    [select clarity first_as_label "Select" "Very Good" "Good" "Average" "Poor" "Very Poor"] </label>

<label> Friendliness
    [select friendliness first_as_label "Select" "Very Good" "Good" "Average" "Poor" "Very Poor"] </label>

<div>
    [submit "Submit"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'software-survey-form':
        ob_start();
        ?>
[section_break section_break-699 "Software Survey"]

<label> How did you hear about? 
    [select select-51 first_as_label "Select" "Email" "Word of Mouth" "Social Media" "Ads" "Search"] </label>

<label> Which platform do you use? </label>
[checkbox checkbox-548 use_label_element "Windows" "Linux" "Mac OS" "Other"]

<label> How would you rate? 
    [rating rating-551 icon:star1 style:0 default:5] </label>

<label> Did you purchase?
    [select purchase first_as_label "Select" "Yes" "No"] </label>

<label> Please let us know negative/positive comments
    [textarea comments x3] </label>

<div>
[submit "Submit"]
</div>
    <?php 
        $data = ob_get_clean();
        break;
    case 'complaint-form':
        ob_start();
        ?>
[section_break section_break-493 "Complaint Form"] Give your honest opinion of what you think [/section_break]

<label> Subject
    [text subject] </label>

<label> Complaint Message 
    [textarea complaint x3] </label>

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Last Name <span class="required">*</span>
            [text* last-name] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Email <span class="required">*</span>
            [email* email autocomplete:email] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Phone <span class="required">*</span>
            [phone* phone-550] </label>
    [/eacf7-col]
[/eacf7-row]

[submit "Submit"]
    <?php 
        $data = ob_get_clean();
        break;
    case 'training-application-form':
        ob_start();
        ?>
[section_break section_break-493 "Training Application Form"] Participant Registration Form [/section_break]

<label> Title
    [text title] </label>

[eacf7-row]
    [eacf7-col col:2]
        <label> First Name <span class="required">*</span>
            [text* first-name] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Last Name <span class="required">*</span>
            [text* last-name] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Email <span class="required">*</span>
            [email* email autocomplete:email] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Phone <span class="required">*</span>
            [phone* phone-550] </label>
    [/eacf7-col]
[/eacf7-row]

[eacf7-row]
    [eacf7-col col:2]
        <label> Position <span class="required">*</span>
            [text* position] </label>
    [/eacf7-col]
    [eacf7-col col:2]
        <label> Company <span class="required">*</span>
            [text* company] </label>
    [/eacf7-col]
[/eacf7-row]

<label> Address
    [address* address-979 format:international required_fields:line1|city|state|zip|country] </label>

<label> Course List
    [select* select-143 first_as_label "Select" "Course A" "Course B" "Course C" "Course D" "Course E" "Course F"] </label>

<label> Additional Message 
    [textarea additional-msg x3] </label>

[submit "Submit"]
    <?php 
        $data = ob_get_clean();
        break;
    default:
        break;
}