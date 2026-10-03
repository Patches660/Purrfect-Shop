import os
import subprocess
import shutil

base_dir = os.path.dirname(os.path.abspath(__file__))
html_path = os.path.join(base_dir, "chatbot_knowledge_base.html")
pdf_path = os.path.join(base_dir, "Purrfect_Shop_Chatbot_Knowledge_Base.pdf")

# Read current HTML
with open(html_path, "r", encoding="utf-8") as f:
    content = f.read()

# Replace any occurrence of the long name with clean "Purrfect Shop"
content = content.replace("Purrfect Shop Cattery & Boutique", "Purrfect Shop")
content = content.replace("เพอร์เฟกต์ ช็อป แคทเทอรี่ & บูติก", "เพอร์เฟกต์ ช็อป")
content = content.replace("บจก. เพอร์เฟกต์ แคท ช็อป", "บจก. เพอร์เฟกต์ ช็อป (Purrfect Shop Co., Ltd.)")
content = content.replace("Grand Opening Cattery", "Grand Opening Purrfect Shop")

with open(html_path, "w", encoding="utf-8") as f:
    f.write(content)

print("[OK] Updated HTML with clean shop name: 'Purrfect Shop'")

# Generate PDF
edge_p = r"C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe"
chrome_p = r"C:\Program Files\Google\Chrome\Application\chrome.exe"
browser_exe = edge_p if os.path.exists(edge_p) else (chrome_p if os.path.exists(chrome_p) else None)

if browser_exe:
    cmd = [
        browser_exe,
        "--headless",
        "--disable-gpu",
        "--no-pdf-header-footer",
        f"--print-to-pdf={pdf_path}",
        html_path
    ]
    res = subprocess.run(cmd, capture_output=True, text=True)
    if os.path.exists(pdf_path):
        size_kb = os.path.getsize(pdf_path) / 1024
        print(f"[OK] PDF successfully regenerated! File: {pdf_path} ({size_kb:.1f} KB)")
        
        art_pdf = os.path.join(r"C:\Users\Windows\.gemini\antigravity\brain\63a36a4c-6ead-4c37-aede-64d365cc7888", "Purrfect_Shop_Chatbot_Knowledge_Base.pdf")
        shutil.copy2(pdf_path, art_pdf)
        print(f"[OK] Copied to artifacts directory: {art_pdf}")
