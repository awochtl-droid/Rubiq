---
name: Doug Grom and LPL Financial temporarily removed from website
description: Full HTML blocks for Doug Grom + LPL references across all pages — stored for quick re-addition
type: project
---

Doug Grom and LPL Financial references were removed from the website on 2026-03-25 at the user's request. All HTML is preserved below for easy restoration.

**Why:** User requested temporary removal — expects to add back later.

**How to apply:** When the user asks to add Doug back, restore these blocks to the exact locations noted below.

---

## 1. index.html — Team grid card (was at ~line 1218, inside the `who-are-grid` div)

Insert this as the second child of the `who-are-grid` div, after the Andreas Wochtl card. Also restore the grid to `grid-template-columns:repeat(2,1fr)`.

```html
      <!-- Doug Grom -->
      <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(26,94,168,0.15);border-radius:12px;
                  overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.12),0 8px 24px rgba(0,0,0,0.08);
                  display:flex;flex-direction:column;">
        <div style="height:300px;overflow:hidden;background:#0F1724;">
          <picture>
            <source srcset="Media/doug_headshot.webp" type="image/webp" />
            <img src="Media/doug_headshot.jpg"
                 alt="Doug Grom, Private Wealth Advisor at Rubiq Financial Partners"
                 width="600" height="300"
                 style="width:100%;height:100%;object-fit:contain;object-position:center center;
                        filter:brightness(1.05) contrast(1.05);" loading="lazy" decoding="async" />
          </picture>
        </div>
        <div style="padding:1.75rem 2rem;display:flex;flex-direction:column;flex:1;">
          <h3 style="font-family:'Playfair Display',serif;font-size:1.3rem;font-weight:600;
                     color:white;margin-bottom:0.2rem;">Doug Grom</h3>
          <p style="font-size:0.75rem;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;
                    color:#C49A5A;margin-bottom:1rem;">Private Wealth Advisor</p>
          <p style="font-size:0.9rem;line-height:1.7;color:rgba(255,255,255,0.60);font-weight:300;">
            Doug brings deep expertise in corporate executive compensation, estate planning, and
            tax-efficient portfolio strategy. He partners closely with clients to navigate complex
            financial lives with clarity, discipline, and a long-term perspective.
          </p>
          <div style="display:flex;gap:0.75rem;margin-top:auto;padding-top:1.5rem;flex-wrap:wrap;">
            <a href="https://www.linkedin.com/in/ejadvisordouggrom/" target="_blank" rel="noopener noreferrer"
               style="display:inline-flex;align-items:center;gap:6px;font-size:0.75rem;font-weight:600;
                      letter-spacing:0.04em;color:rgba(255,255,255,0.55);text-decoration:none;
                      padding:0.4rem 0.85rem;border-radius:100px;border:1px solid rgba(255,255,255,0.12);
                      transition:color 0.18s ease,border-color 0.18s ease;"
               onmouseover="this.style.color='#C49A5A';this.style.borderColor='rgba(196,154,74,0.45)'"
               onmouseout="this.style.color='rgba(255,255,255,0.55)';this.style.borderColor='rgba(255,255,255,0.12)'">
              <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
              LinkedIn
            </a>
            <a href="https://brokercheck.finra.org/individual/summary/4914277" target="_blank" rel="noopener noreferrer"
               style="display:inline-flex;align-items:center;gap:6px;font-size:0.75rem;font-weight:600;
                      letter-spacing:0.04em;color:rgba(255,255,255,0.55);text-decoration:none;
                      padding:0.4rem 0.85rem;border-radius:100px;border:1px solid rgba(255,255,255,0.12);
                      transition:color 0.18s ease,border-color 0.18s ease;"
               onmouseover="this.style.color='#C49A5A';this.style.borderColor='rgba(196,154,74,0.45)'"
               onmouseout="this.style.color='rgba(255,255,255,0.55)';this.style.borderColor='rgba(255,255,255,0.12)'">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4"/><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z"/></svg>
              FINRA
            </a>
            <a href="https://adviserinfo.sec.gov/individual/summary/4914277" target="_blank" rel="noopener noreferrer"
               style="display:inline-flex;align-items:center;gap:6px;font-size:0.75rem;font-weight:600;
                      letter-spacing:0.04em;color:rgba(255,255,255,0.55);text-decoration:none;
                      padding:0.4rem 0.85rem;border-radius:100px;border:1px solid rgba(255,255,255,0.12);
                      transition:color 0.18s ease,border-color 0.18s ease;"
               onmouseover="this.style.color='#C49A5A';this.style.borderColor='rgba(196,154,74,0.45)'"
               onmouseout="this.style.color='rgba(255,255,255,0.55)';this.style.borderColor='rgba(255,255,255,0.12)'">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4"/><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z"/></svg>
              SEC
            </a>
          </div>
        </div>
      </div>
```

## 2. Pages/our-team.html — Full team card (was at ~line 671)

Insert after the Andreas Wochtl card closing `</div>`, before the closing of the team cards container.

```html
      <!-- ── Doug Grom ── -->
      <div class="team-card bg-white rounded-lg overflow-hidden"
           style="box-shadow:0 2px 8px rgba(26,94,168,0.08),0 8px 32px rgba(26,94,168,0.06);">

        <!-- Photo -->
        <div style="position:relative;overflow:hidden;height:340px;background:#ffffff;">
          <picture>
            <source srcset="../Media/doug_headshot.webp" type="image/webp" />
            <img src="../Media/doug_headshot.jpg"
                 alt="Doug Grom, Private Wealth Advisor at Rubiq Financial Partners, specializing in corporate executives and estate planning"
                 width="600" height="340"
                 style="width:100%;height:100%;object-fit:contain;object-position:center center;
                        filter:brightness(1.05) contrast(1.03) saturate(1.05);" loading="lazy" decoding="async" />
          </picture>
        </div>

        <!-- Content -->
        <div class="p-7">
          <h3 style="font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:600;
                     color:#0F1724;margin-bottom:0.2rem;">Doug Grom</h3>
          <p style="font-size:0.8rem;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;
                    color:#1A5EA8;margin-bottom:0.875rem;">Private Wealth Advisor</p>

          <!-- Credentials -->
          <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:1.25rem;">
            <span style="display:inline-block;font-size:0.72rem;font-weight:600;letter-spacing:0.04em;
                         color:#2C3E55;background:rgba(44,62,85,0.06);border:1px solid rgba(44,62,85,0.15);
                         border-radius:4px;padding:0.2rem 0.6rem;">Villanova University</span>
            <span style="display:inline-block;font-size:0.72rem;font-weight:600;letter-spacing:0.04em;
                         color:#2C3E55;background:rgba(44,62,85,0.06);border:1px solid rgba(44,62,85,0.15);
                         border-radius:4px;padding:0.2rem 0.6rem;">Series 7 &amp; 66</span>
            <span style="display:inline-block;font-size:0.72rem;font-weight:600;letter-spacing:0.04em;
                         color:#2C3E55;background:rgba(44,62,85,0.06);border:1px solid rgba(44,62,85,0.15);
                         border-radius:4px;padding:0.2rem 0.6rem;">20+ Years Experience</span>
          </div>

          <!-- Focus pills -->
          <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:1.25rem;">
            <span class="focus-pill">Corporate Executives</span>
            <span class="focus-pill">Executive Compensation</span>
            <span class="focus-pill">Estate Planning</span>
          </div>

          <p style="font-size:0.9rem;line-height:1.7;color:#4A5568;font-weight:300;margin-bottom:1.25rem;">
            Doug brings over 20 years of financial services experience to Rubiq, with deep expertise
            in serving corporate executives and their families. A Villanova University graduate, he
            built his career at Northwestern Mutual, Edward Jones, Citizens Securities, and Merrill
            Lynch — where he served as a Senior Financial Advisor and Senior Portfolio Manager —
            before joining Rubiq to advise clients in a fully independent, fee-based fiduciary environment.
            He is Chairman of the Springfield Township Rotary Foundation Board and an Advisory Board
            Member of the East Falls Development Corporation.
          </p>

          <!-- Experience outline -->
          <div style="margin-bottom:1.25rem;">
            <p style="font-size:0.75rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;
                      color:#2C3E55;margin-bottom:0.75rem;">Areas of Focus</p>
            <div class="exp-item">
              <div class="exp-dot"></div>
              <p style="font-size:0.875rem;line-height:1.6;color:#4A5568;font-weight:300;margin:0;">
                Executive compensation strategy — equity awards, deferred compensation plans, and
                concentrated stock position management for corporate executives and senior leaders
              </p>
            </div>
            <div class="exp-item">
              <div class="exp-dot"></div>
              <p style="font-size:0.875rem;line-height:1.6;color:#4A5568;font-weight:300;margin:0;">
                Liquidity event and career transition planning — helping executives navigate
                retirement, role changes, and wealth crystallization events with full financial clarity
              </p>
            </div>
            <div class="exp-item">
              <div class="exp-dot"></div>
              <p style="font-size:0.875rem;line-height:1.6;color:#4A5568;font-weight:300;margin:0;">
                Legacy and estate planning — multi-generational wealth transfer strategies, trust
                structures, and long-term preservation for executive families
              </p>
            </div>
            <div class="exp-item">
              <div class="exp-dot"></div>
              <p style="font-size:0.875rem;line-height:1.6;color:#4A5568;font-weight:300;margin:0;">
                Tax minimization and retirement income planning — coordinating portfolio withdrawals,
                Social Security timing, and tax strategies across the full retirement arc
              </p>
            </div>
          </div>

          <!-- Social links -->
          <div class="flex flex-wrap items-center gap-4">
            <a href="https://www.linkedin.com/in/ejadvisordouggrom/" target="_blank" rel="noopener noreferrer"
               style="display:flex;align-items:center;gap:6px;font-size:0.8rem;font-weight:500;
                      color:#1A5EA8;text-decoration:none;transition:opacity 0.18s;"
               onmouseover="this.style.opacity='0.70'" onmouseout="this.style.opacity='1'">
              <svg width="14" height="14" fill="#1A5EA8" viewBox="0 0 24 24">
                <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2z"/>
                <circle cx="4" cy="4" r="2" fill="#1A5EA8"/>
              </svg>
              LinkedIn
            </a>
            <a href="https://brokercheck.finra.org/individual/summary/4914277" target="_blank" rel="noopener noreferrer"
               style="display:flex;align-items:center;gap:6px;font-size:0.8rem;font-weight:500;
                      color:#1A5EA8;text-decoration:none;transition:opacity 0.18s;"
               onmouseover="this.style.opacity='0.70'" onmouseout="this.style.opacity='1'">
              <svg width="14" height="14" fill="none" stroke="#1A5EA8" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
              </svg>
              FINRA BrokerCheck
            </a>
            <a href="https://adviserinfo.sec.gov/individual/summary/4914277" target="_blank" rel="noopener noreferrer"
               style="display:flex;align-items:center;gap:6px;font-size:0.8rem;font-weight:500;
                      color:#1A5EA8;text-decoration:none;transition:opacity 0.18s;"
               onmouseover="this.style.opacity='0.70'" onmouseout="this.style.opacity='1'">
              <svg width="14" height="14" fill="none" stroke="#1A5EA8" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
              </svg>
              SEC
            </a>
          </div>
        </div>
      </div>
```

## 3. Pages/our-team.html — Schema.org structured data (was at ~line 192)

Insert this JSON-LD Person entry back into the `employee` array in the schema block:

```json
      {
        "@type": "Person",
        "@id": "https://www.rubiqfinancial.com/#doug-grom",
        "name": "Doug Grom",
        "jobTitle": "Private Wealth Advisor",
        "worksFor": { "@id": "https://www.rubiqfinancial.com/#organization" },
        "description": "Private Wealth Advisor at Rubiq Financial Partners. Specializes in serving corporate executives and their families with estate planning and executive compensation strategies.",
        "sameAs": [
          "https://www.linkedin.com/in/ejadvisordouggrom/",
          "https://brokercheck.finra.org/individual/summary/4914277",
          "https://adviserinfo.sec.gov/individual/summary/4914277"
        ],
        "image": "https://www.rubiqfinancial.com/Media/doug_headshot.jpg"
      }
```

## 4. Pages/sitemap.html — Description text (was at ~line 371)

Change the Our Team description back to:
```
Profiles for Andreas Wochtl and Doug Grom, our independent wealth advisors.
```

---

# LPL Financial References (also removed 2026-03-25)

## 5. Footer disclaimer — ALL pages (30+ files)

The footer one-liner across most pages was:
```
Advisory services offered through Wealthcare Advisory Partners. Brokerage products offered through LPL Financial, Member FINRA/SIPC.
```
The "Brokerage products offered through LPL Financial, Member FINRA/SIPC." sentence was removed. To restore, add it back after "Wealthcare Advisory Partners."

Pages with this footer (plain text version): business-planning, case-study-entrepreneur, case-study-executive, case-study-real-estate, case-study-private-wealth, case-study-uhnw, contact, edge-alternatives, edge-leverage, edge-real-estate, faq, fee-structure, insurance, insights, investment-management, our-capabilities, our-clients, our-team, resources-alternative-investments, resources-annuities, resources-dst-1031, resources-roth-conversion, resources-rebalancing, resources-opportunity-zones, resources-reits, resources-secondaries, resources-section-351, retirement-planning, schedule, resources-tax-loss-harvesting, sitemap, tax-planning.

Pages with HTML link version (404.html, index.html):
```html
Brokerage products offered through LPL Financial, Member <a href="https://www.finra.org" target="_blank" rel="noopener noreferrer" style="color:rgba(255,255,255,0.28);text-decoration:underline;">FINRA</a>/<a href="https://www.sipc.org" target="_blank" rel="noopener noreferrer" style="color:rgba(255,255,255,0.28);text-decoration:underline;">SIPC</a>.
```

## 6. Extended disclosure — index.html and fee-structure.html

The sentence removed from the extended disclosure paragraph was:
```
Rubiq Financial is also affiliated with LPL Financial LLC ("LPL Financial"), a registered investment adviser and broker-dealer, Member FINRA/SIPC.
```
To restore, insert this sentence after the sentence ending with `("SEC").` in the expanded disclosures section.

## 7. Pages/our-team.html — LPL Financial badge card

Insert after the Wealthcare Advisory Partners card in the affiliations section:
```html
      <!-- LPL Financial -->
      <a href="https://www.lpl.com/" target="_blank" rel="noopener noreferrer" style="text-decoration:none;">
        <div style="background:white;border-radius:10px;padding:1.75rem 1.5rem;height:100%;
                    border:1px solid rgba(26,94,168,0.08);
                    box-shadow:0 2px 8px rgba(26,94,168,0.06),0 6px 24px rgba(26,94,168,0.04);
                    transition:transform 0.20s ease,box-shadow 0.20s ease;"
             onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 4px 16px rgba(26,94,168,0.12),0 12px 36px rgba(26,94,168,0.08)'"
             onmouseout="this.style.transform='';this.style.boxShadow='0 2px 8px rgba(26,94,168,0.06),0 6px 24px rgba(26,94,168,0.04)'">
          <div style="width:36px;height:3px;background:linear-gradient(90deg,#1A5EA8,#C49A5A);
                      border-radius:2px;margin-bottom:1rem;"></div>
          <div style="font-family:'Playfair Display',serif;font-size:1rem;font-weight:600;
                      color:#0F1724;margin-bottom:0.5rem;line-height:1.3;">
            LPL Financial
          </div>
          <div style="font-size:0.75rem;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;
                      color:#1A5EA8;margin-bottom:0.6rem;">Broker-Dealer · Member FINRA/SIPC</div>
          <div style="font-size:0.8rem;color:#4A5568;font-weight:300;line-height:1.6;">
            America's largest independent broker-dealer, providing brokerage, investment advisory, and financial planning services and technology to support independent advisors.
          </div>
        </div>
      </a>
```

## 8. Pages/terms-of-use.html — Regulatory section

The removed text was:
```html
Brokerage products are offered through LPL Financial, Member
      <a href="https://www.finra.org" target="_blank" rel="noopener noreferrer" style="color:#1A5EA8;">FINRA</a> /
      <a href="https://www.sipc.org" target="_blank" rel="noopener noreferrer" style="color:#1A5EA8;">SIPC</a>.
```
Insert after "1940." in the Regulatory Information section.

Also in terms-of-use.html and privacy-policy.html footer boxes, the text was:
```
Advisory services offered through Wealthcare Advisory Partners, a registered investment
        adviser. Brokerage products offered through LPL Financial, Member FINRA/SIPC. Rubiq
```
Restore by adding back "Brokerage products offered through LPL Financial, Member FINRA/SIPC." between "adviser." and "Rubiq".
