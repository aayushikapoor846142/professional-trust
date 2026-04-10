@extends('layouts.app')
@section('content')

<section class="cdsTYMainsite-support01-section-wrap">
    <div class="cdsTYMainsite-support01-header-wrapper">
        <!-- Left Side: Image -->
        <div class="cdsTYMainsite-support01-header-background">
            <img src="{{url('assets/images/cover9.jpeg') }}" alt="" />
            <div class="cdsTYMainsite-support01-header-overlay"></div>
            <div class="cdsTYMainsite-support01-section-wrap-top">
                <h1><span>Let's Bring Back Our Profession</span> – Immigration Services by Ethical Licensed Immigration Professionals Only.</h1>
                <p>TrustVisory Ltd. is a pioneering initiative to address the growing problem of Unauthorized Practitioners(UAPs) and entities offering unauthorized immigration services for a fee.</p>
            </div>
        </div>

        <!-- Centered Content -->
        <div class="cdsTYMainsite-support01-header-cover">
            <!-- Right Side: Text -->
            <div class="cdsTYMainsite-support01-text-container">
                <div class="cds-content">
                    <form id="payment-form" method="post" action="{{ url('razorpay/pay-for-support') }}">
                        @csrf 
                        @include("components.support-via-razorpay")
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="cdsTYMainsite-support01-sub-menu-wrapper cdsTYMainsite-support01-main-wrapper-container-desktop-view">
    <div class="cdsTYMainsite-support01-main-wrapper-container ">
        <div class="CDSMainsite-general-content-external-tab-buttons CDSMainsite-general-content-external-tab-buttons-desktop cds-support-page-tab-bx">
            <a href="#" data-tab="0" class="active">Our Ambitious 100 Days Plan</a>
            <a href="#" data-tab="1">Our Achievements</a>
            <a href="#" data-tab="2">How Do We Combat Illegal Activities?</a>
            <a href="#" data-tab="3">Our Mission</a>
        </div>
    </div>
</section>

<section class="cdsTYMainsite-support01-main-wrapper">
    <div class="cdsTYMainsite-support01-main-wrapper-container">
        <div class="cdsTYMainsite-support01-main-wrapper-container-inner list-support-content-panels">
            <div class="cdsTYMainsite-support01-main-wrapper-container cdsTYMainsite-support01-main-wrapper-container-mobile-view">
                <div class="CDSMainsite-general-content-external-tab-buttons cds-support-page-tab-bx CDSMainsite-general-content-external-tab-buttons-mb">
                    <a href="#" data-tab="0" class="active">Our Ambitious 100 Days Plan</a>
                    <a href="#" data-tab="1">Our Achievements</a>
                    <a href="#" data-tab="2">How Do We Combat Illegal Activities?</a>
                    <a href="#" data-tab="3">Our Mission</a>
                </div>
            </div>

            <div class="CDSMainsite-general-content-external-tabs-container" id="tabsComponent">
                <div class="CDSMainsite-general-content-external-tab-wrapper">
                    <div class="CDSMainsite-general-content-external-tab-content active" id="tab1">
                        <div class="CDSMainsite-special01-content-list-container">
                            <div class="CDSMainsite-special01-content-list-header">
                                <h4 class="reg-heading">Our Ambitious <span style="color: #bb0202;">100 Days</span> Plan for Bringing Our Profession Back & Promote Ethical, Trustworthy Immigration Practices</h4>
                            </div>
                            <div class="CDSMainsite-special01-content-list-body">
                                <div class="CDSMainsite-special01-content-list">
                                    <div class="CDSMainsite-special01-content-list-segments">
                                        <div class="CDSMainsite-special01-content-list-segments-header">
                                            <span>Bringing Our Profession Back</span>
                                        </div>
                                        <div class="CDSMainsite-special01-content-list-segments-body">
                                            <h4>1. Restore the Profession to Licensed and Regulated Professionals</h4>
                                            <ul>
                                                <li><i class="fa-solid fa-circle-check"></i>Reinforce that only licensed and authorized professionals can offer immigration services.</li>
                                                <li><i class="fa-solid fa-circle-check"></i>Educate the public about verifying credentials.</li>
                                                <li><i class="fa-solid fa-circle-check"></i>Actively challenge the normalization of unlicensed practice.</li>
                                                <li><i class="fa-solid fa-circle-check"></i>Support proper licensing pathways for aspiring professionals.</li>
                                            </ul>

                                            <h4>2. Achieve ZERO Unauthorized Practitioners and Build a Living UAPs/UACE-SN List</h4>
                                            <ul>
                                                <li><i class="fa-solid fa-circle-check"></i>Launch a zero-tolerance campaign against UAPs.</li>
                                                <li><i class="fa-solid fa-circle-check"></i>Detect and verify unauthorized service providers.</li>
                                                <li><i class="fa-solid fa-circle-check"></i>Publish a real-time, public registry if UAPs for awareness and transparency.</li>
                                            </ul>
                                            <h4>3. Expose Enablers and Campaign Against Indirect Immigration Service Providers</h4>
                                            <ul>
                                                <li><i class="fa-solid fa-circle-check"></i>Identify institutions and organizations that accredit or promote unlicensed actors.</li>
                                                <li><i class="fa-solid fa-circle-check"></i>Conduct public campaigns to challenge these practices.</li>
                                                <li><i class="fa-solid fa-circle-check"></i>Advocate for regulatory reforms to eliminate systemic enablers.</li>
                                                <li><i class="fa-solid fa-circle-check"></i>Monitor collaborations between education agents and unlicensed consultants.</li>
                                                <li><i class="fa-solid fa-circle-check"></i>Make UAPs enabling and Proliferating organizations accountable for their actions.</li>
                                            </ul>
                                            <h4>4. Create Collective Marketing Opportunities for Licensed Professionals</h4>
                                            <ul>
                                                <li><i class="fa-solid fa-circle-check"></i>Modify platform to connect licensed consultants with clients.</li>
                                                <li><i class="fa-solid fa-circle-check"></i>Implement trust indexes, ethical certifications, and profile rankings.</li>
                                                <li><i class="fa-solid fa-circle-check"></i>Drive visibility and client flow toward verified ethical professionals.</li>
                                                <li><i class="fa-solid fa-circle-check"></i> Launch targeted digital campaigns to educate clients on choosing the right professional.</li>
                                                <li><i class="fa-solid fa-circle-check"></i>Highlight success stories and testimonials of Licensed ethical professionals.</li>
                                            </ul>
                                            <h4>5. Invest Support Fees into Technological Advancement</h4>
                                            <ul>
                                                <li><i class="fa-solid fa-circle-check"></i>Allocate funding into building modern tools for compliance and security.</li>
                                                <li><i class="fa-solid fa-circle-check"></i>Support innovation that benefits licensed professionals.</li>
                                                <li><i class="fa-solid fa-circle-check"></i> Ensure the profession evolves with technological and digital trends.</li>
                                                <li><i class="fa-solid fa-circle-check"></i>Develop digital verification and referral tools for licensed professionals.</li>
                                                <li><i class="fa-solid fa-circle-check"></i>Launch platforms for continuing education, compliance tracking, and practice support.</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="CDSMainsite-special01-content-list-segments">
                                        <div class="CDSMainsite-special01-content-list-segments-header">
                                            <span>Promote Ethical, Trustworthy Immigration Practices</span>
                                        </div>
                                        <div class="CDSMainsite-special01-content-list-segments-body">
                                            <h4>6. Launch Real-Time Fraud Detection and Prevention Ecosystems</h4>
                                            <ul>
                                                <li><i class="fa-solid fa-circle-check"></i> Build hybrid systems using both human intel and A.I. to detect fraud.</li>
                                                <li><i class="fa-solid fa-circle-check"></i> Monitor social media, websites, and ads for suspicious activities.</li>
                                                <li><i class="fa-solid fa-circle-check"></i> Predict and flag risky behavior before damage occurs.</li>
                                            </ul>
                                            <h4>7. Promote an Industry-Wide Culture of Ethics and Integrity</h4>
                                            <ul>
                                                <li><i class="fa-solid fa-circle-check"></i> Establish and promote a universal Code of Ethical Conduct.</li>
                                                <li><i class="fa-solid fa-circle-check"></i> Encourage ethical peer reviews and reporting mechanisms.</li>
                                                <li><i class="fa-solid fa-circle-check"></i> Celebrate and spotlight professionals who uphold integrity.</li>
                                            </ul>
                                            <h4>8. Hold Unethical Professionals Accountable Through Public Advocacy</h4>
                                            <ul>
                                                <li><i class="fa-solid fa-circle-check"></i> Monitor and report unethical behavior even among licensed individuals.</li>
                                                <li><i class="fa-solid fa-circle-check"></i> Collect client reports and evidence for action.</li>
                                                <li><i class="fa-solid fa-circle-check"></i> Escalate concerns to relevant oversight bodies for disciplinary action.</li>
                                            </ul>
                                            <h4>9. Strong Advocacy with Government, Regulatory, and International Bodies</h4>
                                            <ul>
                                                <li><i class="fa-solid fa-circle-check"></i> Engage national and international stakeholders.</li>
                                                <li><i class="fa-solid fa-circle-check"></i> Advocate for stricter regulations and cross-border enforcement.</li>
                                                <li><i class="fa-solid fa-circle-check"></i> Represent ethical professionals in policymaking and reform.</li>
                                            </ul>
                                            <h4>10. Build a Unified, Transparent, and Future-Ready Immigration Ecosystem</h4>
                                            <ul>
                                                <li>
                                                    <i class="fa-solid fa-circle-check"></i>
                                                    Integrate technology, policy, and advocacy to safeguard clients.
                                                </li>
                                                <li><i class="fa-solid fa-circle-check"></i> Create an ecosystem that empowers ethical actors.</li>
                                                <li><i class="fa-solid fa-circle-check"></i> Ensure long-term sustainability and accountability in immigration services.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="CDSMainsite-general-content-external-tab-content" id="tab2">
                        <div class="CDSMainsite-general-content-section">
                            <div class="CDSMainsite-general-content-section-container">
                                <div class="CDSMainsite-general-content-section-container-header"></div>
                                <div class="CDSMainsite-general-content-section-container-body">
                                    <div class="CDSMainsite-general-content-section-container-body-inner">
                                        <h4 class="reg-heading">Our Achievements</h4>
                                        <span class="span-title-compact">Fraud Reporting, Victim Support & Communication Mechanisms</span>
                                        <ul>
                                            <li><i class="fa-sharp fa-solid fa-circle-check"></i>Established a public and professional-facing fraud reporting mechanism to report unauthorized immigration services.</li>
                                            <li><i class="fa-sharp fa-solid fa-circle-check"></i>Launched a dedicated Fraud Tip Line specifically for AAIP (Alberta Advantage Immigration Program).</li>
                                            <li>
                                                <i class="fa-sharp fa-solid fa-circle-check"></i>Provided pro-bono legal and procedural support to victims of immigration fraud, including assistance in filing official complaints and cases.
                                            </li>
                                            <li><i class="fa-sharp fa-solid fa-circle-check"></i>Initiated a public awareness WhatsApp group to support victims and share ongoing scam updates and preventive tips.</li>
                                            <li><i class="fa-sharp fa-solid fa-circle-check"></i>Enabled a public-facing UAP/UACE-SN database for transparency and awareness.</li>
                                            <li><i class="fa-sharp fa-solid fa-circle-check"></i>Set up alert tags and dedicated public help pages for reporting and verifying fraudulent professionals.</li>
                                            <li><i class="fa-sharp fa-solid fa-circle-check"></i>Integrated victim feedback channels allowing affected individuals to submit detailed reports and experiences.</li>
                                        </ul>

                                        <span class="span-title-compact"> AI-Driven Risk Detection and Monitoring</span>
                                        <ul>
                                            <li><i class="fa-sharp fa-solid fa-circle-check"></i>Developed an AI-based fraud detection investigator model, capable of profiling and tagging suspicious activity across platforms.</li>
                                            <li><i class="fa-sharp fa-solid fa-circle-check"></i>Created "ScanVisor" and "VideoVisor" AI tools to analyze social media posts and videos, generating real-time risk-based profiles.</li>
                                            <li><i class="fa-sharp fa-solid fa-circle-check"></i>Designed a fraud risk classification system (1–5 scale) based on over 120 unique parameters per subject.</li>
                                            <li><i class="fa-sharp fa-solid fa-circle-check"></i>Implemented Level 1 (overt) and Level 2 (covert) investigations as part of the profiling process.</li>
                                        </ul>

                                        <span class="span-title-compact"> Classifications & Profiling Systems</span>
                                        <ul>
                                            <li>
                                                <i class="fa-sharp fa-solid fa-circle-check"></i>Pioneered the first-ever classification system for UAPs (Unauthorized Professionals) and UACE-SNs (Unauthorized and Suspicious Corporate
                                                Entities) with:
                                                <ul class="reg-list">
                                                    <li>4 Major Categories</li>
                                                    <li>23 Sub-Classifications</li>
                                                </ul>
                                            </li>
                                            <li><i class="fa-sharp fa-solid fa-circle-check"></i>Introduced a dynamic risk score model that evolves as new intelligence and evidence is collected.</li>
                                        </ul>

                                        <span class="span-title-compact">Investigations & Exposés</span>
                                        <ul>
                                            <li>
                                                <i class="fa-sharp fa-solid fa-circle-check"></i>Investigated and exposed multiple immigration scams, including:
                                                <ul class="reg-list">
                                                    <li>KIJIJI LMIA scams</li>
                                                    <li>Caregiver certificate selling rings</li>
                                                    <li>Nigeria-based RCIC impersonation networks</li>
                                                    <li>Cross-jurisdictional human trafficking ring spanning India, Singapore, and the USA</li>
                                                    <li>MyImmigrationServices LMIA scam (covert operation)</li>
                                                    <li>Kellen Services scam (highlighted in Toronto Star)</li>
                                                </ul>
                                            </li>
                                            <li><i class="fa-sharp fa-solid fa-circle-check"></i>Conducted over a dozen covert operations to collect evidence and document illegal practices.</li>
                                            <li><i class="fa-sharp fa-solid fa-circle-check"></i>Infiltrated Facebook & WhatsApp networks used to peddle LMIA documents and exploit migrant workers.</li>
                                            <li><i class="fa-sharp fa-solid fa-circle-check"></i>Investigated ICEF accreditation showing how it is used by UAPs for false legitimacy.</li>
                                        </ul>

                                        <span class="span-title-compact"> Communication & Enforcement Strategy</span>
                                        <ul>
                                            <li>
                                                <i class="fa-sharp fa-solid fa-circle-check"></i>Sent 4,726 direct communication emails to unauthorized providers urging them to cease services, with:
                                                <ul class="reg-list">
                                                    <li>652 UAPs publicly listed</li>
                                                    <li>1,456 under private watch</li>
                                                    <li>Remaining under investigation</li>
                                                </ul>
                                            </li>
                                            <li><i class="fa-sharp fa-solid fa-circle-check"></i>Developed escalation levels (1 to 5) based on behavior, evidence, and responsiveness.</li>
                                            <li><i class="fa-sharp fa-solid fa-circle-check"></i>Disabled payment gateways used by flagged UAPs to prevent financial transactions.</li>
                                            <li><i class="fa-sharp fa-solid fa-circle-check"></i>Facilitated takedowns of websites and social media influencers involved in unauthorized immigration marketing.</li>
                                            <li><i class="fa-sharp fa-solid fa-circle-check"></i>Shut down scam aggregators, including NextMigrats and ongoing work on others.</li>
                                        </ul>

                                        <span class="span-title-compact"> Public Education & Content Development</span>
                                        <ul>
                                            <li><i class="fa-sharp fa-solid fa-circle-check"></i>Ran multilingual public awareness campaigns across social platforms.</li>
                                            <li><i class="fa-sharp fa-solid fa-circle-check"></i>Published detailed articles and knowledgebases aimed at helping the public identify red flags.</li>
                                            <li><i class="fa-sharp fa-solid fa-circle-check"></i>Introduced educational resources that demystify immigration processes and professional responsibilities.</li>
                                            <li><i class="fa-sharp fa-solid fa-circle-check"></i>Supported transparency with listing reconsideration options and "Right to be Forgotten" policies to balance enforcement with reform.</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="CDSMainsite-general-content-section-container-footer"></div>
                            </div>
                        </div>
                    </div>
                    <div class="CDSMainsite-general-content-external-tab-content" id="tab3">
                        <div class="CDSMainsite-general-content-section">
                            <div class="CDSMainsite-general-content-section-container">
                                <div class="CDSMainsite-general-content-section-container-header"></div>
                                <div class="CDSMainsite-general-content-section-container-body">
                                    <div class="CDSMainsite-general-content-section-container-body-inner">
                                        <h5 class="reg-heading">How Do We Combat Unauthorized Practioners, Unethical Professionals & Illegal Activities?</h5>
                                        <p>
                                            TrustVisory is a <strong> non-profit agency</strong> dedicated to creating awareness and alert systems for the public regarding such individuals, entities, and employers. We aim to bring awareness
                                            to job seekers before they fall prey to exploitative employers, recruitment agencies, and professionals who engage in unethical practices. At TrustVisory, we are committed to ending this
                                            exploitation through the following initiatives:
                                        </p>
                                        <h6>Advanced Technology Utilization:</h6>
                                        <ul>
                                            <li>Implementing cutting-edge AI and machine learning algorithms to analyze vast amounts of data.</li>
                                            <li>Using advanced software to track and monitor suspicious activities in real-time.</li>
                                        </ul>
                                        <h6>Data Analytics:</h6>
                                        <ul class="reg-list">
                                            <li>Uncovering the identities and operational details of human traffickers and illegal immigration facilitators.</li>
                                            <li>Compiling evidence-based profiles of unethical actors.</li>
                                        </ul>
                                        <h6>Centralized Database Creation:</h6>
                                        <ul class="reg-list">
                                            <li>Establishing a comprehensive, centralized database to catalog identified unethical actors.</li>
                                            <li>Ensuring the database is regularly updated with verified information.</li>
                                        </ul>
                                        <h6>Public Accessibility:</h6>
                                        <ul class="reg-list">
                                            <li>Making the database accessible to the public, enabling individuals to verify the legitimacy of immigration practitioners.</li>
                                            <li>Providing an easy-to-use interface for the public to search and review profiles of identified unethical actors.</li>
                                        </ul>
                                        <h6>Empowering Informed Decisions:</h6>
                                        <ul class="reg-list">
                                            <li>Educating the public on the importance of using verified and ethical immigration practitioners.</li>
                                            <li>Offering resources and guidance on how to identify and avoid fraudulent operators.</li>
                                        </ul>
                                        <h6>Community Engagement:</h6>
                                        <ul class="reg-list">
                                            <li>Encouraging the community to report suspicious activities and practitioners.</li>
                                            <li>Collaborating with other organizations and stakeholders to strengthen the fight against unethical practices.</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="CDSMainsite-general-content-section-container-footer"></div>
                            </div>
                        </div>
                    </div>
                    <div class="CDSMainsite-general-content-external-tab-content" id="tab4">
                        <div class="CDSMainsite-general-content-section">
                            <div class="CDSMainsite-general-content-section-container">
                                <div class="CDSMainsite-general-content-section-container-header"></div>
                                <div class="CDSMainsite-general-content-section-container-body">
                                    <div class="CDSMainsite-general-content-section-container-body-inner">
                                        <h5 class="reg-heading">Our Mission</h5>
                                        <p>
                                            Our mission goes beyond protecting individuals; we aim to protect the integrity of immigration systems and the safety of communities. By highlighting these dangerous actors and educating the
                                            public, TrustVisory is fighting for an immigration system that is safe, transparent, and fair.
                                        </p>
                                        <p>
                                            This initiative is a commitment to ensuring that every individual’s journey toward a better life remains dignified, free from exploitation, and protected from those who would turn hope into a
                                            commodity.
                                        </p>
                                        <p>Together, we can dismantle these networks, defend the vulnerable, and build a future where immigration upholds integrity, humanity, and justice.</p>
                                    </div>
                                </div>
                                <div class="CDSMainsite-general-content-section-container-footer"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="cdsTYMainsite-support01-main-wrapper-container-inner-sidebar list-support-badge">
            <div class="cdsTYMainsite-support01-main-wrapper-container-inner-sidebar-wrap">
                <div class="cdsTYMainsite-support01-main-contributory-badges-section cds-badges-block">
                    <div class="cdsTYMainsite-support01-main-contributory-badges-section-header">
                        <h3>Contributory badges to ethical immigration services</h3>
                    </div>
                    <div class="cdsTYMainsite-support01-main-contributory-badges-section-body">
                        <div class="cdsTYMainsite-support01-main-contributory-badges-list cds-badges-list-bx">
                            <div class="cdsTYMainsite-support01-main-contributory-badges-segment">
                                <img src="{{url('assets/images/badges/platinum-red.png') }}" alt="" />
                                <span>Platinum Elite</span>
                                <div class="cds-badge-earn">
                                    <small> <b>Best of the best</b></small>
                                </div>
                            </div>
                            <div class="cdsTYMainsite-support01-main-contributory-badges-segment">
                                <img src="{{url('assets/images/badges/platinum.png') }}" alt="" />
                                <span>Platinum</span>
                                <div class="cds-badge-earn">
                                    <small>
                                        <b>
                                            200,000 points <br />
                                            ($20,000)
                                        </b>
                                    </small>
                                </div>
                            </div>
                            <div class="cdsTYMainsite-support01-main-contributory-badges-segment">
                                <img src="{{url('assets/images/badges/gold.png') }}" alt="" />
                                <span>Gold</span>
                                <div class="cds-badge-earn">
                                    <small>
                                        <b>
                                            50,000 points <br />
                                            ($5000)
                                        </b>
                                    </small>
                                </div>
                            </div>
                            <div class="cdsTYMainsite-support01-main-contributory-badges-segment">
                                <img src="{{url('assets/images/badges/silver.png') }}" alt="" />
                                <span>Silver</span>
                                <div class="cds-badge-earn">
                                    <small>
                                        <b>
                                            5000 points <br />
                                            ($500)
                                        </b>
                                    </small>
                                </div>
                            </div>
                            <div class="cdsTYMainsite-support01-main-contributory-badges-segment">
                                <img src="{{url('assets/images/badges/bronze.png') }}" alt="" />
                                <span>Bronze</span>
                                <div class="cds-badge-earn">
                                    <small>
                                        <b>
                                            2500 points <br />
                                            ($250)
                                        </b>
                                    </small>
                                </div>
                            </div>
                            <div class="cdsTYMainsite-support01-main-contributory-badges-segment">
                                <img src="{{url('assets/images/badges/silver-fern.png') }}" alt="" />
                                <span>Silver Fern</span>
                                <div class="cds-badge-earn mt-2">
                                    <small>
                                        <b>
                                            200 points <br />
                                            ($20)
                                        </b>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    const tabLinks = document.querySelectorAll('.CDSMainsite-general-content-external-tab-buttons-desktop a');
    const tabLinks2 = document.querySelectorAll('.CDSMainsite-general-content-external-tab-buttons-mb a');
    const tabsContainer = document.getElementById('tabsComponent');
    const tabContents = tabsContainer.querySelectorAll('.CDSMainsite-general-content-external-tab-content');
    const loader = tabsContainer.querySelector('.CDSMainsite-general-content-external-loader');

    let resetTimeout;
    let activeTabIndex = 0;

    function activateTab(index) {
        if (index === activeTabIndex) return; // 🔒 prevent reloading the same tab

        clearTimeout(resetTimeout);
        activeTabIndex = index;

        tabsContainer.classList.add('loading');

        setTimeout(() => {
            tabLinks.forEach(link => link.classList.remove('active'));
            tabLinks[index].classList.add('active');

            tabLinks2.forEach(link => link.classList.remove('active'));
            tabLinks2[index].classList.add('active');

            tabContents.forEach((tab, i) => {
                tab.classList.remove('active');
                if (i === index) {
                    tab.classList.add('active');
                }
            });

            tabsContainer.classList.remove('loading');
        }, 500);
    }

    tabLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const index = parseInt(link.getAttribute('data-tab'));
            activateTab(index);
        });
    });
    tabLinks2.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const index = parseInt(link.getAttribute('data-tab'));
            activateTab(index);
        });
    });

    // Detect real clicks (not scrolls)
</script>


@endsection
