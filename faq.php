<?php
$currentPage = 'faq';
$pageTitle   = 'FAQ';
include 'config.php';
include 'functions.php';

$siteName    = getSetting('site_name', 'Lukman Primary School');
$contactPhone = getSetting('contact_phone', '+256 782 284788');
$contactEmail = getSetting('contact_email', 'info@lukmanps.ac.ug');

$pageDescription = 'Frequently asked questions about Lukman Primary School — admissions, fees, boarding, curriculum, PLE examinations, and how to contact us.';
include 'includes/header.php';
include 'includes/breadcrumb.php';
breadcrumb([['label' => 'Frequently Asked Questions']]);
?>

<!-- FAQ Schema Structured Data -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        {
            "@type": "Question",
            "name": "What classes does Lukman Primary School offer?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "We offer classes from Baby Class (Nursery) through Primary 7 (P7). This covers Early Childhood Development (ECD) and the full Uganda National Curriculum up to the Primary Leaving Examination (PLE) level."
            }
        },
        {
            "@type": "Question",
            "name": "Where is Lukman Primary School located?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Lukman Primary School is located in Entebbe Municipality, Wakiso District, Uganda. You can reach us by calling +256 782 284788 or emailing info@lukmanps.ac.ug."
            }
        },
        {
            "@type": "Question",
            "name": "What curriculum does the school follow?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "We follow a dual curriculum: the Uganda National Curriculum (UNC) set by NCDC/UNEB, and an integrated Islamic studies curriculum covering Quran recitation, Arabic language, Islamic studies, and character building."
            }
        },
        {
            "@type": "Question",
            "name": "Does the school have a boarding section?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Yes. We have a well-supervised boarding section with separate dormitories for boys and girls. Boarders enjoy three meals daily, evening guided study, and a mosque on campus for daily prayers."
            }
        },
        {
            "@type": "Question",
            "name": "How do I apply for admission?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Visit our Admissions page to fill in the online inquiry form, or come to the school office in person. Bring the child's birth certificate and any previous school reports. We accept applications throughout the year."
            }
        }
    ]
}
</script>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1>Frequently Asked Questions</h1>
            <p>Find answers to common questions about <?php echo htmlspecialchars($siteName); ?></p>
        </div>
    </div>

    <!-- FAQ Section -->
    <section class="section-pad">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <!-- Admissions -->
                    <h3 style="color:var(--dark-blue);font-weight:700;margin-bottom:1.5rem;"><i class="fas fa-user-plus me-2" style="color:var(--primary);"></i>Admissions</h3>
                    <div class="accordion mb-5" id="faqAdmissions">
                        <div class="faq-item">
                            <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq1">
                                <span>What classes are available at Lukman Primary School?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div id="faq1" class="collapse show" data-bs-parent="#faqAdmissions">
                                <div class="faq-answer">
                                    We offer classes from <strong>Baby Class (Nursery)</strong> all the way to <strong>Primary 7 (P7)</strong>. This includes Baby Class, Middle Class, Top Class (ECD), P1 through P7. We also have a boarding section for P4&ndash;P7 pupils.
                                </div>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq2">
                                <span>How do I apply for admission?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div id="faq2" class="collapse" data-bs-parent="#faqAdmissions">
                                <div class="faq-answer">
                                    You can apply in two ways: (1) Fill in the <a href="admissions.php">online admission inquiry form</a> on our website and our admin office will contact you. (2) Visit the school administration office in person during working hours (Mon&ndash;Fri, 7:30 AM&ndash;5:00 PM). Please bring the child's birth certificate and any previous school reports.
                                </div>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq3">
                                <span>What are the admission requirements?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div id="faq3" class="collapse" data-bs-parent="#faqAdmissions">
                                <div class="faq-answer">
                                    Required documents include: <strong>Child's birth certificate</strong>, <strong>passport photos</strong> (2&ndash;4), <strong>previous school report cards</strong> (for P2 and above transfers), and <strong>immunisation card</strong> for ECD pupils. An admission assessment may be conducted for P3 and above classes.
                                </div>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq4">
                                <span>When does the school year begin?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div id="faq4" class="collapse" data-bs-parent="#faqAdmissions">
                                <div class="faq-answer">
                                    Uganda's school calendar has <strong>three terms</strong>: Term 1 begins in February, Term 2 begins in June, and Term 3 begins in September. We accept admissions at the start of each term, subject to availability.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fees & Payments -->
                    <h3 style="color:var(--dark-blue);font-weight:700;margin-bottom:1.5rem;"><i class="fas fa-money-bill-wave me-2" style="color:var(--primary);"></i>Fees &amp; Payments</h3>
                    <div class="accordion mb-5" id="faqFees">
                        <div class="faq-item">
                            <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq5">
                                <span>How much are the school fees?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div id="faq5" class="collapse" data-bs-parent="#faqFees">
                                <div class="faq-answer">
                                    School fees vary by class and whether the pupil is a day scholar or boarder. Please visit our <a href="admissions.php">Admissions page</a> for the current fees structure, or contact the school office directly at <a href="tel:<?php echo preg_replace('/\s+/','',$contactPhone); ?>"><?php echo htmlspecialchars($contactPhone); ?></a> for the latest figures.
                                </div>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq6">
                                <span>How are fees paid?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div id="faq6" class="collapse" data-bs-parent="#faqFees">
                                <div class="faq-answer">
                                    Fees can be paid via <strong>bank transfer</strong> or <strong>mobile money</strong>. Payment should be made before or at the start of each term. A receipt is issued for all payments. Part-payments can be arranged with the bursar in genuine hardship cases.
                                </div>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq7">
                                <span>Are there any scholarships or fee waivers available?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div id="faq7" class="collapse" data-bs-parent="#faqFees">
                                <div class="faq-answer">
                                    We occasionally offer bursaries for academically excellent pupils from low-income families. Contact the headteacher's office to discuss your circumstances. Priority is given to orphans and children in financial hardship who demonstrate strong academic potential.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Boarding -->
                    <h3 style="color:var(--dark-blue);font-weight:700;margin-bottom:1.5rem;"><i class="fas fa-bed me-2" style="color:var(--primary);"></i>Boarding</h3>
                    <div class="accordion mb-5" id="faqBoarding">
                        <div class="faq-item">
                            <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq8">
                                <span>What classes can board at the school?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div id="faq8" class="collapse" data-bs-parent="#faqBoarding">
                                <div class="faq-answer">
                                    Boarding is available for pupils in <strong>Primary 4 (P4)</strong> through <strong>Primary 7 (P7)</strong>. Younger pupils (Baby Class&ndash;P3) are day scholars only.
                                </div>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq9">
                                <span>What does boarding include?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div id="faq9" class="collapse" data-bs-parent="#faqBoarding">
                                <div class="faq-answer">
                                    Boarding covers: <strong>accommodation</strong> in supervised dormitories, <strong>three meals</strong> per day (breakfast, lunch, supper), <strong>evening prep</strong> (guided study sessions), access to the <strong>school mosque</strong> for daily prayers, and 24-hour welfare staffing for health and safety.
                                </div>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq10">
                                <span>Can parents visit boarding pupils during term?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div id="faq10" class="collapse" data-bs-parent="#faqBoarding">
                                <div class="faq-answer">
                                    Yes. Visiting days are scheduled each term — normally once mid-term on a Saturday. Parents and authorised guardians may visit between 9:00 AM and 4:00 PM on visiting days. Emergency visits outside of these days require prior approval from the headteacher.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Curriculum & Academics -->
                    <h3 style="color:var(--dark-blue);font-weight:700;margin-bottom:1.5rem;"><i class="fas fa-book-open me-2" style="color:var(--primary);"></i>Curriculum &amp; Academics</h3>
                    <div class="accordion mb-5" id="faqCurriculum">
                        <div class="faq-item">
                            <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq11">
                                <span>What curriculum does the school follow?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div id="faq11" class="collapse" data-bs-parent="#faqCurriculum">
                                <div class="faq-answer">
                                    We follow a <strong>dual curriculum</strong>: (1) the <strong>Uganda National Curriculum (NCDC/UNEB)</strong> covering English, Mathematics, Science, Social Studies, Local Language (Luganda), Religious Education, ICT, PE, and Music; and (2) our own <strong>Islamic Studies curriculum</strong> covering Quran recitation (Tajweed), Arabic language, Islamic Studies &amp; Fiqh, Seerah, and character building (Adab).
                                </div>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq12">
                                <span>What are the school subjects for PLE candidates (P7)?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div id="faq12" class="collapse" data-bs-parent="#faqCurriculum">
                                <div class="faq-answer">
                                    Uganda's PLE examines four compulsory subjects: <strong>English Language</strong>, <strong>Mathematics</strong>, <strong>Science</strong>, and <strong>Social Studies &amp; Religious Education</strong>. Our P7 pupils also sit internal assessments in Islamic Studies and Arabic.
                                </div>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq13">
                                <span>Do you offer computer studies / ICT?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div id="faq13" class="collapse" data-bs-parent="#faqCurriculum">
                                <div class="faq-answer">
                                    Yes. ICT and Computer Studies are taught from <strong>Primary 4 upwards</strong>. Our computer lab is equipped with PCs and internet access to support digital literacy.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PLE & Exams -->
                    <h3 style="color:var(--dark-blue);font-weight:700;margin-bottom:1.5rem;"><i class="fas fa-graduation-cap me-2" style="color:var(--primary);"></i>PLE &amp; Examinations</h3>
                    <div class="accordion mb-5" id="faqPLE">
                        <div class="faq-item">
                            <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq14">
                                <span>How has the school performed in PLE?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div id="faq14" class="collapse" data-bs-parent="#faqPLE">
                                <div class="faq-answer">
                                    Lukman Primary School has a strong track record in the Primary Leaving Examinations. We consistently produce Division 1 and Division 2 passes. Visit our <a href="results.php">PLE Results page</a> to see our performance breakdown by year.
                                </div>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq15">
                                <span>When are PLE exams held?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div id="faq15" class="collapse" data-bs-parent="#faqPLE">
                                <div class="faq-answer">
                                    Uganda National Examinations Board (UNEB) administers PLE in <strong>October/November</strong> each year. Results are released approximately in January the following year.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact & General -->
                    <h3 style="color:var(--dark-blue);font-weight:700;margin-bottom:1.5rem;"><i class="fas fa-info-circle me-2" style="color:var(--primary);"></i>General</h3>
                    <div class="accordion mb-5" id="faqGeneral">
                        <div class="faq-item">
                            <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq16">
                                <span>Where is the school located?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div id="faq16" class="collapse" data-bs-parent="#faqGeneral">
                                <div class="faq-answer">
                                    Lukman Primary School is located in <strong>Entebbe Municipality, Wakiso District, Uganda</strong>. Contact us at <a href="tel:<?php echo preg_replace('/\s+/','',$contactPhone); ?>"><?php echo htmlspecialchars($contactPhone); ?></a> or <a href="mailto:<?php echo htmlspecialchars($contactEmail); ?>"><?php echo htmlspecialchars($contactEmail); ?></a> for directions.
                                </div>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq17">
                                <span>What are the school's office hours?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div id="faq17" class="collapse" data-bs-parent="#faqGeneral">
                                <div class="faq-answer">
                                    The administration office is open <strong>Monday to Friday, 7:30 AM &ndash; 5:00 PM</strong>. On Saturdays (during term), opening is 8:00 AM &ndash; 1:00 PM. The office is closed on Sundays and public holidays.
                                </div>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq18">
                                <span>Does the school have a mosque?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div id="faq18" class="collapse" data-bs-parent="#faqGeneral">
                                <div class="faq-answer">
                                    Yes. We have a mosque on campus where all Muslim pupils and staff perform the five daily prayers. Islamic prayers and Quran recitation are integrated into the daily school routine.
                                </div>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question" data-bs-toggle="collapse" data-bs-target="#faq19">
                                <span>Is your school only for Muslim pupils?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div id="faq19" class="collapse" data-bs-parent="#faqGeneral">
                                <div class="faq-answer">
                                    No. While Lukman Primary School was founded on Islamic values, we warmly welcome pupils of all faiths. Our Uganda National Curriculum includes Religious Education for all faith backgrounds. The Islamic Studies sessions are part of our school culture but all children are respected equally.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Still have questions -->
                    <div style="background:var(--gray-light);border-radius:12px;padding:2rem;text-align:center;margin-top:2rem;">
                        <i class="fas fa-question-circle" style="font-size:2.5rem;color:var(--primary);margin-bottom:1rem;display:block;"></i>
                        <h4 style="font-weight:700;margin-bottom:0.75rem;">Still have a question?</h4>
                        <p style="color:var(--gray-text);margin-bottom:1.5rem;">Our admin team is happy to help. Reach us by phone, email, or visit the school office.</p>
                        <a href="contact.php" class="btn-primary-custom" style="padding:0.75rem 2rem;"><i class="fas fa-envelope me-2"></i>Contact Us</a>
                    </div>

                </div>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>