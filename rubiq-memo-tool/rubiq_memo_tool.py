import dotenv
dotenv.load_dotenv()

import streamlit as st
import anthropic
import concurrent.futures
import base64
import html
import pathlib
import urllib.parse
from datetime import date

st.set_page_config(page_title="Rubiq Financial Group — Memo Generator", layout="wide")

st.markdown("""
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --blue: #1A5EA8;
        --blue-dark: #133F73;
        --blue-light: #2A74C8;
        --gold: #C49A5A;
        --gold-light: #D4AE6A;
        --gold-dark: #9E7A32;
        --ink: #0F1724;
        --ink-soft: #1C2D44;
        --slate: #2C3E55;
        --parchment: #FAF8F4;
        --mist: #F4F6F9;
    }

    .stApp { background-color: var(--parchment); }

    /* Sidebar */
    section[data-testid="stSidebar"] {
        background: var(--ink) !important;
    }
    section[data-testid="stSidebar"] * {
        color: rgba(255,255,255,0.85) !important;
    }
    section[data-testid="stSidebar"] .stTextInput label,
    section[data-testid="stSidebar"] .stTextArea label,
    section[data-testid="stSidebar"] .stSelectbox label {
        color: var(--gold) !important;
        font-family: 'Inter', sans-serif !important;
        font-weight: 600 !important;
        font-size: 0.75rem !important;
        letter-spacing: 0.10em !important;
        text-transform: uppercase !important;
    }
    section[data-testid="stSidebar"] input,
    section[data-testid="stSidebar"] textarea {
        background: var(--ink-soft) !important;
        border: 1px solid var(--slate) !important;
        color: white !important;
        border-radius: 6px !important;
        font-family: 'Inter', sans-serif !important;
    }
    section[data-testid="stSidebar"] input:focus,
    section[data-testid="stSidebar"] textarea:focus {
        border-color: var(--blue) !important;
        box-shadow: 0 0 0 2px rgba(26,94,168,0.25) !important;
    }
    section[data-testid="stSidebar"] hr {
        border-color: var(--slate) !important;
    }

    /* Primary button */
    .stButton > button[kind="primary"],
    .stButton > button[data-testid="stBaseButton-primary"] {
        background: linear-gradient(135deg, var(--blue), var(--blue-light)) !important;
        color: white !important;
        border: none !important;
        font-family: 'Inter', sans-serif !important;
        font-weight: 600 !important;
        letter-spacing: 0.04em !important;
        text-transform: uppercase !important;
        border-radius: 6px !important;
        padding: 0.6rem 1.5rem !important;
        transition: opacity 0.2s ease, transform 0.2s ease !important;
    }
    .stButton > button[kind="primary"]:hover,
    .stButton > button[data-testid="stBaseButton-primary"]:hover {
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
    }

    /* Secondary buttons */
    .stButton > button[kind="secondary"],
    .stButton > button[data-testid="stBaseButton-secondary"] {
        background: var(--ink-soft) !important;
        color: white !important;
        border: 1px solid var(--slate) !important;
        font-family: 'Inter', sans-serif !important;
        font-weight: 500 !important;
        font-size: 0.8rem !important;
        border-radius: 6px !important;
        transition: opacity 0.2s ease, transform 0.2s ease !important;
    }
    .stButton > button[kind="secondary"]:hover,
    .stButton > button[data-testid="stBaseButton-secondary"]:hover {
        background: var(--slate) !important;
        transform: translateY(-1px) !important;
    }

    /* Header area */
    .rubiq-header {
        background: linear-gradient(160deg, var(--ink), var(--blue-dark) 55%, var(--blue));
        padding: 1.5rem 2.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }
    .rubiq-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--blue), var(--gold));
    }
    .rubiq-header .rubiq-logo {
        height: 50px;
        width: auto;
        flex-shrink: 0;
    }
    .rubiq-header .header-divider {
        width: 1px;
        height: 40px;
        background: linear-gradient(180deg, transparent, var(--gold), transparent);
        flex-shrink: 0;
    }
    .rubiq-header h1 {
        font-family: 'Playfair Display', serif !important;
        font-size: 2rem !important;
        font-weight: 700 !important;
        letter-spacing: -0.03em !important;
        color: white !important;
        margin: 0 !important;
        line-height: 1.15 !important;
    }

    /* Memo panels */
    .memo-panel {
        background: white;
        border: 1px solid rgba(26,94,168,0.10);
        border-radius: 8px;
        padding: 1.75rem;
        min-height: 300px;
        box-shadow: 0 2px 12px rgba(15,23,36,0.06);
    }
    .memo-panel h3 {
        margin-top: 0;
        color: var(--ink-soft);
        font-family: 'Playfair Display', serif;
    }
    .memo-text {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        font-size: 0.95rem;
        line-height: 1.7;
        color: var(--ink-soft);
        white-space: pre-wrap;
    }

    /* Register headings */
    .register-heading {
        font-family: 'Playfair Display', serif;
        font-size: 1.35rem;
        font-weight: 600;
        color: var(--ink-soft);
        letter-spacing: -0.02em;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--gold);
        margin-bottom: 1rem;
    }

    /* Info box */
    .stAlert {
        border-radius: 6px !important;
    }

    /* Misc */
    .stTextArea textarea {
        font-size: 14px;
        font-family: 'Inter', sans-serif !important;
    }
    div[data-testid="stHorizontalBlock"] > div { padding: 0 0.25rem; }

    /* Checkbox styling */
    section[data-testid="stSidebar"] .stCheckbox label span {
        color: rgba(255,255,255,0.85) !important;
    }
</style>
""", unsafe_allow_html=True)

SYSTEM_PROMPTS = {
    "Formal": (
        "You are a senior investment strategist at a boutique financial advisory firm. "
        "Write a long-form institutional investment memo. Open with the macro context and catalyst, "
        "move to security-level analysis, then portfolio rationale, and close with risk considerations. "
        "Use sophisticated vocabulary appropriate for institutional investors. "
        "Be confident and assertive — no hedging language like 'may', 'could', or 'might'. "
        "Write in third person. Do not use bullet points — write in full paragraphs. "
        "Do not include a subject line or email headers."
    ),
    "Condensed": (
        "You are a senior investment strategist at a boutique financial advisory firm. "
        "Write a condensed investment memo in two to three paragraphs. Lead with the trade recommendation, "
        "then bury the macro context. Assume the reader is financially sophisticated. "
        "Be direct and efficient — every sentence should carry weight. "
        "No bullet points, no headers, no hedging. Confident tone throughout. "
        "Do not include a subject line or email headers."
    ),
    "Plain": (
        "You are a trusted financial advisor writing to a smart client who is not a finance professional. "
        "Write a short, clear memo explaining the trade and why it makes sense right now. "
        "No jargon, no acronyms without explanation. Reads like a conversation with a knowledgeable friend. "
        "Keep it to one to two short paragraphs. Warm but professional. "
        "Do not include a subject line or email headers."
    ),
}


def build_user_prompt(ticker, direction, catalyst, rationale, sell_side, client_name, replacement_ticker=""):
    parts = [
        f"Client: {client_name}",
        f"Trade: {direction} {ticker}",
    ]
    if replacement_ticker and replacement_ticker.strip():
        parts.append(f"Replacement: Buy {replacement_ticker.strip()} instead")
        parts.append(
            f"Include a brief description of what {replacement_ticker.strip()} is, "
            f"why it is a suitable replacement for {ticker}, and how it fits into the portfolio."
        )
    parts.append(f"Macro Catalyst: {catalyst}")
    parts.append(f"Rationale: {rationale}")
    if sell_side and sell_side.strip():
        parts.append(f"Sell-Side Position: {sell_side}")
    parts.append(
        "Write the investment memo now. Do not repeat the inputs back — go straight into the memo."
    )
    return "\n".join(parts)


def generate_memo(register, user_prompt):
    """Generate a single memo. Returns (register, text) or (register, error)."""
    try:
        client = anthropic.Anthropic()
        response = client.messages.create(
            model="claude-sonnet-4-20250514",
            max_tokens=2048,
            system=SYSTEM_PROMPTS[register],
            messages=[{"role": "user", "content": user_prompt}],
        )
        return register, response.content[0].text, None
    except Exception as e:
        return register, None, str(e)


SENDER_EMAIL = "andreas@rubiqfinancial.com"


def build_outlook_link(ticker, direction, memo_text, replacement_ticker=""):
    """Build a deeplink URL for Outlook (New) that pre-populates a compose window."""
    today_str = date.today().strftime("%B %d, %Y")
    trade_label = f"{ticker} {direction}"
    if replacement_ticker and replacement_ticker.strip():
        trade_label += f" / Buy {replacement_ticker.strip()}"
    subject = f"Rubiq Financial Group — {trade_label} Update — {today_str}"
    body = memo_text + "\n\n---\nAndreas | Rubiq Financial Group"
    params = urllib.parse.urlencode({
        "subject": subject,
        "body": body,
        "from": SENDER_EMAIL,
    }, quote_via=urllib.parse.quote)
    return f"https://outlook.office.com/mail/deeplink/compose?{params}"


# --- Session state init ---
if "memos" not in st.session_state:
    st.session_state.memos = {}
if "generating" not in st.session_state:
    st.session_state.generating = False

# --- Header ---
_logo_path = pathlib.Path(__file__).parent / "logo.png"
_logo_b64 = base64.b64encode(_logo_path.read_bytes()).decode()
st.markdown(f"""
<div class="rubiq-header">
    <img src="data:image/png;base64,{_logo_b64}" class="rubiq-logo" alt="Rubiq Financial Group" />
    <div class="header-divider"></div>
    <h1>Memo Generator</h1>
</div>
""", unsafe_allow_html=True)

# --- Layout: sidebar inputs, main area outputs ---
with st.sidebar:
    st.header("Trade Details")
    ticker = st.text_input("Ticker", placeholder="e.g. AAPL")
    direction = st.selectbox("Direction", ["Buy", "Sell"])
    replacement_ticker = ""
    if direction == "Sell":
        replacement_ticker = st.text_input(
            "Buy Instead (optional)",
            placeholder="e.g. MSFT — replacement ticker to recommend",
        )
    catalyst = st.text_input("Macro Catalyst", placeholder="e.g. February 2026 BLS Employment Report")
    rationale = st.text_area(
        "Raw Rationale",
        height=150,
        placeholder="2-3 sentences in your own words explaining the trade thesis…",
    )
    sell_side = st.text_input("Sell-Side Position (optional)", placeholder="e.g. Goldman upgraded to Overweight")
    client_name = st.text_input("Client Name", placeholder="e.g. John Smith")

    st.divider()

    # Register selector
    st.subheader("Registers")
    reg_formal = st.checkbox("Formal", value=True)
    reg_condensed = st.checkbox("Condensed", value=True)
    reg_plain = st.checkbox("Plain", value=True)

    can_generate = ticker.strip() and catalyst.strip() and rationale.strip() and client_name.strip()
    selected = []
    if reg_formal:
        selected.append("Formal")
    if reg_condensed:
        selected.append("Condensed")
    if reg_plain:
        selected.append("Plain")

    generate_clicked = st.button(
        "Generate Memos",
        type="primary",
        disabled=not can_generate or len(selected) == 0,
        use_container_width=True,
    )

# --- Generate ---
if generate_clicked and can_generate and selected:
    user_prompt = build_user_prompt(ticker, direction, catalyst, rationale, sell_side, client_name, replacement_ticker)
    st.session_state.memos = {}
    st.session_state._gen_ticker = ticker
    st.session_state._gen_direction = direction
    st.session_state._gen_replacement = replacement_ticker

    with st.spinner("Generating memos…"):
        with concurrent.futures.ThreadPoolExecutor(max_workers=3) as pool:
            futures = {
                pool.submit(generate_memo, reg, user_prompt): reg for reg in selected
            }
            for future in concurrent.futures.as_completed(futures):
                reg, text, error = future.result()
                st.session_state.memos[reg] = {"text": text, "error": error}

# --- Display memos ---
memos = st.session_state.memos
if memos:
    cols = st.columns(len(memos))
    gen_ticker = st.session_state.get("_gen_ticker", ticker)
    gen_direction = st.session_state.get("_gen_direction", direction)
    gen_replacement = st.session_state.get("_gen_replacement", "")

    for col, reg in zip(cols, ["Formal", "Condensed", "Plain"]):
        if reg not in memos:
            continue
        with col:
            st.markdown(f'<div class="register-heading">{reg}</div>', unsafe_allow_html=True)
            memo = memos[reg]
            if memo["error"]:
                st.error(f"API Error: {memo['error']}")
            else:
                safe_text = html.escape(memo["text"]).replace("\n", "<br>")
                st.markdown(
                    f'<div class="memo-panel"><div class="memo-text">{safe_text}</div></div>',
                    unsafe_allow_html=True,
                )

                btn_col1, btn_col2 = st.columns(2)
                with btn_col1:
                    # Copy to clipboard via st.code workaround
                    if st.button("📋 Copy", key=f"copy_{reg}"):
                        st.code(memo["text"], language=None)
                        st.success("Text shown above — select all and copy (Ctrl+C)")

                with btn_col2:
                    outlook_url = build_outlook_link(
                        gen_ticker, gen_direction, memo["text"], gen_replacement
                    )
                    st.markdown(
                        f'<a href="{outlook_url}" target="_blank" '
                        f'style="display:inline-block;padding:0.4rem 1rem;background:#1C2D44;'
                        f'color:white;border-radius:6px;text-decoration:none;font-size:0.85rem;'
                        f'font-family:Inter,sans-serif;text-align:center;width:100%;box-sizing:border-box;">'
                        f'✉️ Outlook Draft</a>',
                        unsafe_allow_html=True,
                    )
elif not generate_clicked:
    st.info("Fill in the trade details on the left and click **Generate Memos** to get started.")
