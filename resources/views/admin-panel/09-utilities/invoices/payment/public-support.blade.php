@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{ url('assets/plugins/select2/select2.min.css') }}">
 <section class="cds-t21n-breadcrumbs-section">
        <div class="container">
            <div class="row">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Support Initiative</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>
    {{-- content --}}
    <section id="cds-t21n-content-section" class="cds-content mt-md-4 mt-lg-5 mb-5 pt-4 pt-md-0">
        <div class="container">
            
            <div class="row">
                <form id="payment-form" method="post" action="{{ url('pay-for-support') }}">
                    @csrf
                    <div class="row">
                        <div class="col-xl-6 col-md-12 col-lg-5 mt-3 mt-lg-0">
                            <div class="cds-t21n-content-section-sidebar">
                                <div class="cds-t21n-content-section-sidebar-support-form-content">
                                    <div class="cds-t21n-content-section-sidebar-support-form-content-header">
                                        <img src="{{url('assets/frontend/images/banner-support.svg') }}" alt=""
                                            class="Diner Icon">
                                        <span>Let's Join Together</span>
                                        <h4 class="headingh2"> Exposing the Shadows: Combating Modern-Day Slavery and
                                            Illegal Immigration Networks in Global Immigration.</h4>
                                    </div>
                                    <div class="cds-t21n-content-section-sidebar-support-body">
                                        <div class="cds-t35n-content-support-form-section-para">
                                            <div class="cds-t35n-content-support-form-section-para-highlight">
                                                <p>Modern-day slavery, illegal immigration, and human trafficking are
                                                    pressing issues affecting nations like Canada, the United States,
                                                    Australia, New Zealand, and the United Kingdom. Every year, millions
                                                    of hopeful individuals embark on the immigration journey, seeking
                                                    new opportunities and a better life for themselves and their
                                                    families. Yet, for far too many, this path is overshadowed by
                                                    unethical actors who exploit their dreams for profit. These
                                                    individuals aren’t just scammers; they are integral players in
                                                    illegal immigration networks that push vulnerable people into
                                                    exploitative situations, often involving forced labor, abuse, and
                                                    control. </p>
                                                <p><strong>This is the most ambitious project ever undertaken, and it
                                                        marks the first time ever in the immigration industry where are
                                                        not only directly engaging with these Unauthorized Practitioners
                                                        (UAPs), Unauthorized Corporate Entities (UACEs), and Unethical
                                                        Employers and Related Professionals (UERPs) but also creating a
                                                        database of such individuals and entities for public awareness.
                                                    </strong></p>
                                            </div>
                                            <div class="cds-t35n-content-support-form-section-para-details">
                                                <h5>Why is Exposing These Elements So Important?</h5>
                                                <ul>
                                                    <li><i class="fa-sharp fa-solid fa-circle-check"></i>
                                                        <strong>Strengthening National Security :</strong> Unscrupulous
                                                        agents facilitate illegal immigration, compromising national
                                                        security by enabling organized crime and trafficking.</li>
                                                    <li><i class="fa-sharp fa-solid fa-circle-check"></i>
                                                        <strong>Safeguarding Public Health and Social Services
                                                            :</strong> Illegal immigration strains health and social
                                                        systems, leaving trafficked individuals without proper care.
                                                    </li>
                                                    <li><i class="fa-sharp fa-solid fa-circle-check"></i>
                                                        <strong>Preventing Human Rights Violations :</strong> These
                                                        networks deny victims their basic rights, subjecting them to
                                                        forced labor, withheld wages, and violence.</li>
                                                    <li><i class="fa-sharp fa-solid fa-circle-check"></i>
                                                        <strong>Reducing Economic Exploitation :</strong> Trafficked
                                                        individuals are coerced into low-paying jobs, depressing wages
                                                        and conditions; exposing these practices protects fair labor
                                                        standards.</li>
                                                    <li><i class="fa-sharp fa-solid fa-circle-check"></i>
                                                        <strong>Promoting Community Safety :</strong> Criminal elements
                                                        involved in trafficking increase local crime rates, compromising
                                                        community safety.</li>
                                                    <li><i class="fa-sharp fa-solid fa-circle-check"></i>
                                                        <strong>Preserving the Integrity of Immigration Processes
                                                            :</strong> Unethical actors erode trust in immigration
                                                        systems; exposing them restores faith in fair and transparent
                                                        processes.</li>
                                                    <li><i class="fa-sharp fa-solid fa-circle-check"></i>
                                                        <strong>Dismantling the Business Model of Exploitation
                                                            :</strong> Exposing traffickers cuts off financial
                                                        incentives, weakening their operations.</li>
                                                    <li><i class="fa-sharp fa-solid fa-circle-check"></i>
                                                        <strong>Protecting Legal Migrants :</strong> Unethical networks
                                                        stigmatize legal immigrants; exposing these networks clarifies
                                                        fair immigration practices.</li>
                                                    <li><i class="fa-sharp fa-solid fa-circle-check"></i>
                                                        <strong>Empowering Vulnerable Populations :</strong> Shedding
                                                        light on exploitation empowers marginalized groups to avoid
                                                        exploitation and find safe, legitimate pathways.</li>
                                                    <li><i class="fa-sharp fa-solid fa-circle-check"></i>
                                                        <strong>Raising Awareness and Building Community Vigilance
                                                            :</strong> Public knowledge of risks associated with illegal
                                                        immigration and unethical practitioners fosters community
                                                        vigilance and protection for potential victims.</li>
                                                </ul>
                                                <h5>How Do We Combat This?</h5>
                                                <p>TrustVisory is a <strong> non-profit agency</strong> dedicated to
                                                    creating awareness and alert systems for the public regarding such
                                                    individuals, entities, and employers. We aim to bring awareness to
                                                    job seekers before they fall prey to exploitative employers,
                                                    recruitment agencies, and professionals who engage in unethical
                                                    practices. At TrustVisory, we are committed to ending this
                                                    exploitation through the following initiatives:</p>
                                                <h6>Advanced Technology Utilization:</h6>
                                                <ul class="cds-t35n-content-support-form-section-ul-reg-list">
                                                    <li> Implementing cutting-edge AI and machine learning algorithms to
                                                        analyze vast amounts of data.</li>
                                                    <li> Using advanced software to track and monitor suspicious
                                                        activities in real-time.</li>
                                                </ul>
                                                <h6>Data Analytics:</h6>
                                                <ul class="cds-t35n-content-support-form-section-ul-reg-list">
                                                    <li> Uncovering the identities and operational details of human
                                                        traffickers and illegal immigration facilitators.</li>
                                                    <li> Compiling evidence-based profiles of unethical actors.</li>
                                                </ul>
                                                <h6>Centralized Database Creation:</h6>
                                                <ul class="cds-t35n-content-support-form-section-ul-reg-list">
                                                    <li> Establishing a comprehensive, centralized database to catalog
                                                        identified unethical actors.</li>
                                                    <li> Ensuring the database is regularly updated with verified
                                                        information.</li>
                                                </ul>
                                                <h6>Public Accessibility:</h6>
                                                <ul class="cds-t35n-content-support-form-section-ul-reg-list">
                                                    <li> Making the database accessible to the public, enabling
                                                        individuals to verify the legitimacy of immigration
                                                        practitioners.</li>
                                                    <li> Providing an easy-to-use interface for the public to search and
                                                        review profiles of identified unethical actors.</li>
                                                </ul>
                                                <h6>Empowering Informed Decisions:</h6>
                                                <ul class="cds-t35n-content-support-form-section-ul-reg-list">
                                                    <li> Educating the public on the importance of using verified and
                                                        ethical immigration practitioners.</li>
                                                    <li> Offering resources and guidance on how to identify and avoid
                                                        fraudulent operators.</li>
                                                </ul>
                                                <h6>Community Engagement:</h6>
                                                <ul class="cds-t35n-content-support-form-section-ul-reg-list">
                                                    <li> Encouraging the community to report suspicious activities and
                                                        practitioners.</li>
                                                    <li> Collaborating with other organizations and stakeholders to
                                                        strengthen the fight against unethical practices.</li>
                                                </ul>
                                                <h5>Our Mission</h5>
                                                <p>Our mission goes beyond protecting individuals; we aim to protect the
                                                    integrity of immigration systems and the safety of communities. By
                                                    highlighting these dangerous actors and educating the public,
                                                    TrustVisory is fighting for an immigration system that is safe,
                                                    transparent, and fair.
                                                </p>
                                                <p>This initiative is a commitment to ensuring that every individual’s
                                                    journey toward a better life remains dignified, free from
                                                    exploitation, and protected from those who would turn hope into a
                                                    commodity.
                                                </p>
                                                <p>Together, we can dismantle these networks, defend the vulnerable, and
                                                    build a future where immigration upholds integrity, humanity, and
                                                    justice.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-12 col-lg-7 mt-4 mt-lg-0">
                            @include("components.support-via-stripe")
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection
